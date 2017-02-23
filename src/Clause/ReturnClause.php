<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;

final class ReturnClause implements ClauseInterface
{
    const IDENTIFIER = 'RETURN';

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
