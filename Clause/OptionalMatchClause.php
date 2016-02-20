<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;

class OptionalMatchClause extends MatchClause
{
    const IDENTIFIER = 'OPTIONAL MATCH';

    /**
     * {@inheritdoc}
     */
    public function identifier(): string
    {
        return self::IDENTIFIER;
    }
}
