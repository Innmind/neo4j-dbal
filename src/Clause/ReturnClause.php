<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause;

final class ReturnClause implements Clause
{
    private const IDENTIFIER = 'RETURN';

    private array $variables;

    public function __construct(string ...$variables)
    {
        $this->variables = $variables;
    }

    public function identifier(): string
    {
        return self::IDENTIFIER;
    }

    public function cypher(): string
    {
        return \implode(', ', $this->variables);
    }
}
