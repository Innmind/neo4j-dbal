<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause;

final class MatchClause implements Clause, PathAware
{
    use PathAware\PathAware;

    private const IDENTIFIER = 'MATCH';

    public function __construct(Expression\Path $path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function identifier(): string
    {
        return self::IDENTIFIER;
    }
}
