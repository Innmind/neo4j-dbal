<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Query;

use Innmind\Neo4j\DBAL\{
    Query as QueryInterface,
    Clause,
    Exception\NonParametrableClause,
    Exception\NonPathAwareClause,
    Exception\NonMergeClause,
    Exception\LogicException,
};
use Innmind\Immutable\{
    Map,
    Sequence,
};
use function Innmind\Immutable\unwrap;

final class Query implements QueryInterface
{
    /** @var Sequence<Clause> */
    private Sequence $clauses;

    public function __construct()
    {
        $this->clauses = Sequence::of(Clause::class);
    }

    public function cypher(): string
    {
        $previous = $this->clauses->first();
        $clauses = $this->clauses->drop(1);
        $cypher = $previous->identifier().' '.$previous->cypher();

        foreach (unwrap($clauses) as $clause) {
            if ($clause->identifier() === $previous->identifier()) {
                $cypher .= ', ';
            } else {
                $cypher .= ' '.$clause->identifier().' ';
            }

            $cypher .= $clause->cypher();
            $previous = $clause;
        }

        return $cypher;
    }

    public function parameters(): Map
    {
        /** @var Sequence<Clause\Parametrable> [description] */
        $parametrables = $this->clauses->filter(function(Clause $clause): bool {
            return $clause instanceof Clause\Parametrable;
        });

        /** @var Map<string, Parameter> */
        return $parametrables->reduce(
            Map::of('string', Parameter::class),
            function(Map $carry, Clause\Parametrable $clause): Map {
                return $carry->merge($clause->parameters());
            },
        );
    }

    public function hasParameters(): bool
    {
        return !$this->parameters()->empty();
    }

    /**
     * Match the given node
     */
    public function match(string $variable = null, string ...$labels): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\MatchClause(
                Clause\Expression\Path::startWithNode($variable, ...$labels),
            ),
        );

        return $query;
    }

    /**
     * Add a OPTIONAL MATCh clause
     */
    public function maybeMatch(string $variable = null, string ...$labels): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\OptionalMatchClause(
                Clause\Expression\Path::startWithNode($variable, ...$labels),
            ),
        );

        return $query;
    }

    /**
     * Attach parameters to the last clause
     *
     * @param array<string, mixed> $parameters
     *
     * @throws NonParametrableClause
     */
    public function withParameters(array $parameters): self
    {
        $query = $this;

        /** @var mixed $parameter */
        foreach ($parameters as $key => $parameter) {
            $query = $query->withParameter($key, $parameter);
        }

        return $query;
    }

    /**
     * Attach the given parameter to the last clause
     *
     * @param mixed $parameter
     *
     * @throws NonParametrableClause
     */
    public function withParameter(string $key, $parameter): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\Parametrable) {
            throw new NonParametrableClause;
        }

        $clause = $clause->withParameter($key, $parameter);
        $query = new self;
        $query->clauses = $this
            ->clauses
            ->dropEnd(1)
            ->add($clause);

        return $query;
    }

    /**
     * Specify a set of properties to be matched
     *
     * @param array<string, string> $properties
     *
     * @throws NonPathAwareClause
     */
    public function withProperties(array $properties): self
    {
        $query = $this;

        foreach ($properties as $property => $cypher) {
            $query = $query->withProperty($property, $cypher);
        }

        return $query;
    }

    /**
     * Specify a property to be matched
     *
     * @throws NonPathAwareClause
     */
    public function withProperty(string $property, string $cypher): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAware) {
            throw new NonPathAwareClause;
        }

        $clause = $clause->withProperty($property, $cypher);
        $query = new self;
        $query->clauses = $this
            ->clauses
            ->dropEnd(1)
            ->add($clause);

        return $query;
    }

    /**
     * Match the node linked to the previous declared node match
     *
     * @throws NonPathAwareClause
     */
    public function linkedTo(string $variable = null, string ...$labels): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAware) {
            throw new NonPathAwareClause;
        }

        $clause = $clause->linkedTo($variable, ...$labels);
        $query = new self;
        $query->clauses = $this
            ->clauses
            ->dropEnd(1)
            ->add($clause);

        return $query;
    }

    /**
     * Specify the type of relationship for the last match clause
     *
     * @param 'both'|'left'|'right' $direction
     *
     * @throws NonPathAwareClause
     */
    public function through(
        string $type,
        string $variable = null,
        string $direction = 'both'
    ): self {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAware) {
            throw new NonPathAwareClause;
        }

        $clause = $clause->through($variable, $type, $direction);
        $query = new self;
        $query->clauses = $this
            ->clauses
            ->dropEnd(1)
            ->add($clause);

        return $query;
    }

    /**
     * Define the deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceOf(int $distance): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAware) {
            throw new NonPathAwareClause;
        }

        $clause = $clause->withADistanceOf($distance);
        $query = new self;
        $query->clauses = $this
            ->clauses
            ->dropEnd(1)
            ->add($clause);

        return $query;
    }

    /**
     * Define the deepness range of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceBetween(int $min, int $max): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAware) {
            throw new NonPathAwareClause;
        }

        $clause = $clause->withADistanceBetween($min, $max);
        $query = new self;
        $query->clauses = $this
            ->clauses
            ->dropEnd(1)
            ->add($clause);

        return $query;
    }

    /**
     * Define the minimum deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceOfAtLeast(int $distance): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAware) {
            throw new NonPathAwareClause;
        }

        $clause = $clause->withADistanceOfAtLeast($distance);
        $query = new self;
        $query->clauses = $this
            ->clauses
            ->dropEnd(1)
            ->add($clause);

        return $query;
    }

    /**
     * Define the maximum deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withADistanceOfAtMost(int $distance): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAware) {
            throw new NonPathAwareClause;
        }

        $clause = $clause->withADistanceOfAtMost($distance);
        $query = new self;
        $query->clauses = $this
            ->clauses
            ->dropEnd(1)
            ->add($clause);

        return $query;
    }

    /**
     * Define any deepness of the relationship
     *
     * @throws LogicException If no relationship in the path
     */
    public function withAnyDistance(): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAware) {
            throw new NonPathAwareClause;
        }

        $clause = $clause->withAnyDistance();
        $query = new self;
        $query->clauses = $this
            ->clauses
            ->dropEnd(1)
            ->add($clause);

        return $query;
    }

    /**
     * Add a WITH clause
     */
    public function with(string ...$variables): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\WithClause(...$variables),
        );

        return $query;
    }

    /**
     * Add a WHERE clause
     */
    public function where(string $cypher): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\WhereClause($cypher),
        );

        return $query;
    }

    /**
     * Add a SET clause
     */
    public function set(string $cypher): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\SetClause($cypher),
        );

        return $query;
    }

    /**
     * Add a USING clause
     */
    public function using(string $cypher): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\UsingClause($cypher),
        );

        return $query;
    }

    /**
     * Add a UNWIND clause
     */
    public function unwind(string $cypher): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\UnwindClause($cypher),
        );

        return $query;
    }

    /**
     * Add a UNION clause
     */
    public function union(): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\UnionClause,
        );

        return $query;
    }

    /**
     * Add a SKIP clause
     *
     * @see http://neo4j.com/docs/stable/query-skip.html#skip-skip-first-from-expression
     * @param string $cypher Of type string as it may contain operations
     */
    public function skip(string $cypher): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\SkipClause($cypher),
        );

        return $query;
    }

    /**
     * Add a RETURN clause
     */
    public function return(string ...$variables): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\ReturnClause(...$variables),
        );

        return $query;
    }

    /**
     * Add a REMOVE clause
     */
    public function remove(string $cypher): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\RemoveClause($cypher),
        );

        return $query;
    }

    /**
     * Add a ORDER BY clause
     *
     * @param 'asc'|'desc' $direction
     */
    public function orderBy(string $cypher, string $direction = 'asc'): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            Clause\OrderByClause::of($direction, $cypher),
        );

        return $query;
    }

    /**
     * Add a ON MATCH clause
     *
     * @throws NonMergeClause
     */
    public function onMatch(string $cypher): self
    {
        $clause = $this->clauses->last();

        if (
            !$clause instanceof Clause\MergeClause &&
            !$clause instanceof Clause\OnCreateClause
        ) {
            throw new NonMergeClause;
        }

        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\OnMatchClause($cypher),
        );

        return $query;
    }

    /**
     * Add a ON CREATE clause
     *
     * @throws NonMergeClause
     */
    public function onCreate(string $cypher): self
    {
        $clause = $this->clauses->last();

        if (
            !$clause instanceof Clause\MergeClause &&
            !$clause instanceof Clause\OnMatchClause
        ) {
            throw new NonMergeClause;
        }

        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\OnCreateClause($cypher),
        );

        return $query;
    }

    /**
     * Add a MERGE clause
     */
    public function merge(string $variable = null, string ...$labels): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\MergeClause(
                Clause\Expression\Path::startWithNode($variable, ...$labels),
            ),
        );

        return $query;
    }

    /**
     * Add a LIMIT clause
     *
     * @see http://neo4j.com/docs/stable/query-limit.html#limit-return-first-from-expression
     */
    public function limit(string $cypher): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\LimitClause($cypher),
        );

        return $query;
    }

    /**
     * Add a FOREACH clause
     */
    public function foreach(string $cypher): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\ForeachClause($cypher),
        );

        return $query;
    }

    /**
     * Add a DELETE clause
     */
    public function delete(string $variable, bool $detach = false): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\DeleteClause($variable, $detach),
        );

        return $query;
    }

    /**
     * Add a CREATE clause
     *
     * @param list<string> $labels
     */
    public function create(string $variable, string ...$labels): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\CreateClause(
                Clause\Expression\Path::startWithNode($variable, ...$labels),
                false,
            ),
        );

        return $query;
    }

    /**
     * Add a CREATE clause
     */
    public function createUnique(string $variable, string ...$labels): self
    {
        $query = new self;
        $query->clauses = ($this->clauses)(
            new Clause\CreateClause(
                Clause\Expression\Path::startWithNode($variable, ...$labels),
                true,
            ),
        );

        return $query;
    }
}
