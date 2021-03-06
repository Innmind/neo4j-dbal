<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\Clause;

final class CreateClause implements Clause, PathAware
{
    use PathAware\PathAware;

    private const IDENTIFIER = 'CREATE';

    private bool $unique;

    public function __construct(Expression\Path $path, bool $unique)
    {
        $this->path = $path;
        $this->unique = $unique;
    }

    public function identifier(): string
    {
        return self::IDENTIFIER.($this->unique ? ' UNIQUE' : '');
    }
}
