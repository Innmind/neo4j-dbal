<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;

final class DeleteClause implements ClauseInterface
{
    const IDENTIFIER = 'DELETE';

    private $cypher;
    private $detachable = false;

    public function __construct(string $cypher, bool $detachable)
    {
        $this->cypher = $cypher;
        $this->detachable = $detachable;
    }

    /**
     * {@inheritdoc}
     */
    public function identifier(): string
    {
        return $this->detachable ?
            'DETACH ' . self::IDENTIFIER : self::IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->cypher;
    }
}
