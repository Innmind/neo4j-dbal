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
     * @throws InvalidArgumentException if there's no resultDataContents
     *
     * @return PreQueryEvent self
     */
    public function setStatements(array $statements)
    {
        foreach ($statements as $statement) {
            if (
                !isset($statement['resultDataContents']) ||
                $statement['resultDataContents'] !== ['graph', 'row']
            ) {
                throw new \InvalidArgumentException(
                    'A statement must have the key "resultDataContents" set to "[\'graph\', \'row\']"'
                );
            }
        }

        $this->statements = $statements;

        return $this;
    }
}
