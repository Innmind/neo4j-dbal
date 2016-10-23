<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;

class OptionalMatchClause implements ClauseInterface, PathAwareInterface
{
    use PathAware;

    const IDENTIFIER = 'OPTIONAL MATCH';

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
