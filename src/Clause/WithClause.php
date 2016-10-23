<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;

class WithClause implements ClauseInterface
{
    const IDENTIFIER = 'WITH';

    private $variables;

    public function __construct(string ...$variables)
    {
        $this->variables = $variables;
    }

    /**
     * {@inheritdoc}
     */
    public function identifier(): string
    {
        return self::IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return implode(', ', $this->variables);
    }
}
