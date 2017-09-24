<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\{
    Clause,
    Exception\DomainException
};

final class DeleteClause implements Clause
{
    const IDENTIFIER = 'DELETE';

    private $cypher;
    private $detachable = false;

    public function __construct(string $cypher, bool $detachable)
    {
        if (empty($cypher)) {
            throw new DomainException;
        }

        $this->cypher = $cypher;
        $this->detachable = $detachable;
    }

    /**
     * {@inheritdoc}
     */
    public function identifier(): string
    {
        return $this->detachable ?
            'DETACH '.self::IDENTIFIER : self::IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->cypher;
    }
}
