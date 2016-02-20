<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Clause;

use Innmind\Neo4j\DBAL\ClauseInterface;

class CreateClause extends MatchClause
{
    const IDENTIFIER = 'CREATE';

    private $unique;

    public function __construct(
        Expression\Path $path,
        bool $unique
    ) {
        $this->unique = $unique;

        parent::__construct($path);
    }

    /**
     * {@inheritdoc}
     */
    public function identifier(): string
    {
        return self::IDENTIFIER . ($this->unique ? ' UNIQUE' : '');
    }
}
