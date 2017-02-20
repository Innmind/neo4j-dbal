<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\{
    Query\Parameter,
    Clause\ParametrableInterface,
    Exception\NonParametrableClauseException,
    Exception\NonPathAwareClauseException,
    Exception\NonMergeClauseException
};
use Innmind\Immutable\{
    MapInterface,
    Map,
    Stream
};

final class Query implements QueryInterface
{
    private $clauses;
    private $parameters;
    private $cypher;

    public function __construct()
    {
        $this->clauses = new Stream(ClauseInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function cypher(): string
    {
        if ($this->cypher) {
            return $this->cypher;
        }

        $previous = $this->clauses->first();
        $clauses = $this->clauses->drop(1);
        $cypher = $previous->identifier().' '.(string) $previous;

        foreach ($clauses as $clause) {
            if ($clause->identifier() === $previous->identifier()) {
                $cypher .= ', ';
            } else {
                $cypher .= ' '.$clause->identifier().' ';
            }

            $cypher .= (string) $clause;
            $previous = $clause;
        }

        $this->cypher = $cypher;

        return $cypher;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->cypher();
    }

    /**
     * {@inheritdoc}
     */
    public function parameters(): MapInterface
    {
        if ($this->parameters) {
            return $this->parameters;
        }

        return $this->parameters = $this
            ->clauses
            ->filter(function(ClauseInterface $clause): bool {
                return $clause instanceof ParametrableInterface;
            })
            ->reduce(
                new Map('string', Parameter::class),
                function(Map $carry, ParametrableInterface $clause): Map {
                    return $carry->merge($clause->parameters());
                }
            );
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameters(): bool
    {
        return $this->parameters()->size() > 0;
    }

    /**
     * Match the given node
     *
     * @param string $variable
     * @param array $labels
     *
     * @return self
     */
    public function match(string $variable = null, array $labels = []): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\MatchClause(
                Clause\Expression\Path::startWithNode($variable, $labels)
            )
        );

        return $query;
    }

    /**
     * Add a OPTIONAL MATCh clause
     *
     * @param string $variable
     * @param array $labels
     *
     * @return self
     */
    public function maybeMatch(string $variable = null, array $labels = []): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\OptionalMatchClause(
                Clause\Expression\Path::startWithNode($variable, $labels)
            )
        );

        return $query;
    }

    /**
     * Attach parameters to the last clause
     *
     * @param array $parameters
     *
     * @throws NonParametrableClauseException
     *
     * @return self
     */
    public function withParameters(array $parameters): self
    {
        $query = $this;

        foreach ($parameters as $key => $parameter) {
            $query = $query->withParameter($key, $parameter);
        }

        return $query;
    }

    /**
     * Attach the given parameter to the last clause
     *
     * @param string $key
     * @param mixed $parameter
     *
     * @throws NonParametrableClauseException
     *
     * @return self
     */
    public function withParameter(string $key, $parameter): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\ParametrableInterface) {
            throw new NonParametrableClauseException;
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
     * @param array $properties
     *
     * @throws NonPathAwareClauseException
     *
     * @return self
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
     * @param string $property
     * @param string $cypher
     *
     * @throws NonPathAwareClauseException
     *
     * @return self
     */
    public function withProperty(string $property, string $cypher): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAwareInterface) {
            throw new NonPathAwareClauseException;
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
     * @param string $variable
     * @param array $labels
     *
     * @throws NonPathAwareClauseException
     *
     * @return self
     */
    public function linkedTo(string $variable = null, array $labels = []): self
    {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAwareInterface) {
            throw new NonPathAwareClauseException;
        }

        $clause = $clause->linkedTo($variable, $labels);
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
     * @param string $type
     * @param string $variable
     * @param string $direction
     *
     * @throws NonPathAwareClauseException
     *
     * @return self
     */
    public function through(
        string $type,
        string $variable = null,
        string $direction = Clause\Expression\Relationship::BOTH
    ): self {
        $clause = $this->clauses->last();

        if (!$clause instanceof Clause\PathAwareInterface) {
            throw new NonPathAwareClauseException;
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
     * Add a WITH clause
     *
     * @param string $variables
     *
     * @return self
     */
    public function with(string ...$variables): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\WithClause(...$variables)
        );

        return $query;
    }

    /**
     * Add a WHERE clause
     *
     * @param string $cypher
     *
     * @return self
     */
    public function where(string $cypher): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\WhereClause($cypher)
        );

        return $query;
    }

    /**
     * Add a SET clause
     *
     * @param string $cypher
     *
     * @return self
     */
    public function set(string $cypher): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\SetClause($cypher)
        );

        return $query;
    }

    /**
     * Add a USING clause
     *
     * @param string $cypher
     *
     * @return self
     */
    public function using(string $cypher): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\UsingClause($cypher)
        );

        return $query;
    }

    /**
     * Add a UNWIND clause
     *
     * @param string $cypher
     *
     * @return self
     */
    public function unwind(string $cypher): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\UnwindClause($cypher)
        );

        return $query;
    }

    /**
     * Add a UNION clause
     *
     * @return self
     */
    public function union(): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\UnionClause
        );

        return $query;
    }

    /**
     * Add a SKIP clause
     *
     * @see http://neo4j.com/docs/stable/query-skip.html#skip-skip-first-from-expression
     * @param string $cypher Of type string as it may contain operations
     *
     * @return self
     */
    public function skip(string $cypher): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\SkipClause($cypher)
        );

        return $query;
    }

    /**
     * Add a RETURN clause
     *
     * @param string $variables
     *
     * @return self
     */
    public function return(string ...$variables): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\ReturnClause(...$variables)
        );

        return $query;
    }

    /**
     * Add a REMOVE clause
     *
     * @param string $cypher
     *
     * @return self
     */
    public function remove(string $cypher): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\RemoveClause($cypher)
        );

        return $query;
    }

    /**
     * Add a ORDER BY clause
     *
     * @param string $cypher
     * @param string $direction
     *
     * @return self
     */
    public function orderBy(
        string $cypher,
        string $direction = Clause\OrderByClause::ASC
    ): self {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\OrderByClause($cypher, $direction)
        );

        return $query;
    }

    /**
     * Add a ON MATCH clause
     *
     * @param string $cypher
     *
     * @throws NonMergeClauseException
     *
     * @return self
     */
    public function onMatch(string $cypher): self
    {
        $clause = $this->clauses->last();

        if (
            !$clause instanceof Clause\MergeClause &&
            !$clause instanceof Clause\OnCreateClause
        ) {
            throw new NonMergeClauseException;
        }

        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\OnMatchClause($cypher)
        );

        return $query;
    }

    /**
     * Add a ON CREATE clause
     *
     * @param string $cypher
     *
     * @throws NonMergeClauseException
     *
     * @return self
     */
    public function onCreate(string $cypher): self
    {
        $clause = $this->clauses->last();

        if (
            !$clause instanceof Clause\MergeClause &&
            !$clause instanceof Clause\OnMatchClause
        ) {
            throw new NonMergeClauseException;
        }

        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\OnCreateClause($cypher)
        );

        return $query;
    }

    /**
     * Add a MERGE clause
     *
     * @param string $variable
     * @param array $labels
     *
     * @return self
     */
    public function merge(string $variable = null, array $labels = []): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\MergeClause(
                Clause\Expression\Path::startWithNode($variable, $labels)
            )
        );

        return $query;
    }

    /**
     * Add a LIMIT clause
     *
     * @see http://neo4j.com/docs/stable/query-limit.html#limit-return-first-from-expression
     * @param string $cypher
     *
     * @return self
     */
    public function limit(string $cypher): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\LimitClause($cypher)
        );

        return $query;
    }

    /**
     * Add a FOREACH clause
     *
     * @param string $cypher
     *
     * @return self
     */
    public function foreach(string $cypher): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\ForeachClause($cypher)
        );

        return $query;
    }

    /**
     * Add a DELETE clause
     *
     * @param string $variable
     * @param bool $detach
     *
     * @return self
     */
    public function delete(string $variable, bool $detach = false): self
    {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\DeleteClause($variable, $detach)
        );

        return $query;
    }

    /**
     * Add a CREATE clause
     *
     * @param string $variable
     * @param array $labels
     * @param bool $unique
     *
     * @return self
     */
    public function create(
        string $variable,
        array $labels = [],
        bool $unique = false
    ): self {
        $query = new self;
        $query->clauses = $this->clauses->add(
            new Clause\CreateClause(
                Clause\Expression\Path::startWithNode($variable, $labels),
                $unique
            )
        );

        return $query;
    }
}
