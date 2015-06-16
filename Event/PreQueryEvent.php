<?php

namespace Innmind\Neo4j\DBAL\Event;

use Symfony\Component\EventDispatcher\Event;

class PreQueryEvent extends Event
{
    protected $statements;

    public function __construct(array $statements)
    {
        $this->statements = $statements;
    }

    /**
     * Return the statements to be executed
     *
     * @return array
     */
    public function getStatements()
    {
        return $this->statements;
    }

    /**
     * Replace the statements to be executed by the ones passed as argument
     *
     * @param array $statements
     *
     * @return PreQueryEvent self
     */
    public function setStatements(array $statements)
    {
        $this->statements = $statements;

        return $this;
    }
}
