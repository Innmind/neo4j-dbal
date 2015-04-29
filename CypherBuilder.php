<?php

namespace Innmind\Neo4j\DBAL;

class CypherBuilder
{
    protected $keys = [
        Query::SEQUENCE_MATCH => 'MATCH',
        Query::SEQUENCE_OPTIONAL_MATCH => 'OPTIONAL MATCH',
        Query::SEQUENCE_WHERE => 'WHERE',
        Query::SEQUENCE_CREATE => 'CREATE',
        Query::SEQUENCE_MERGE => 'MERGE',
        Query::SEQUENCE_ON_MATCH => 'ON MATCH',
        Query::SEQUENCE_ON_CREATE => 'ON CREATE',
        Query::SEQUENCE_SET => 'SET',
        Query::SEQUENCE_DELETE => 'DELETE',
        Query::SEQUENCE_REMOVE => 'REMOVE',
        Query::SEQUENCE_RETURN => 'RETURN',
    ];

    /**
     * Transform a Query object into a Cypher string
     *
     * @param Query $query
     *
     * @return string Cypher query
     */
    public function getCypher(Query $query)
    {
        $sequence = $query->getSequence();
        $statements = [
            Query::SEQUENCE_MATCH => $query->getMatch(),
            Query::SEQUENCE_OPTIONAL_MATCH => $query->getOptionalMatch(),
            Query::SEQUENCE_WHERE => $query->getWhere(),
            Query::SEQUENCE_CREATE => $query->getCreate(),
            Query::SEQUENCE_MERGE => $query->getMerge(),
            Query::SEQUENCE_ON_MATCH => $query->getOnMatch(),
            Query::SEQUENCE_ON_CREATE => $query->getOnCreate(),
            Query::SEQUENCE_SET => $query->getSet(),
            Query::SEQUENCE_DELETE => $query->getDelete(),
            Query::SEQUENCE_REMOVE => $query->getRemove(),
        ];
        $cypher = [];
        $currentStatement = [];

        for ($i = 0, $l = count($sequence); $i < $l; $i++) {
            $key = $sequence[$i];
            $keyChanged = ($i > 0 && $key !== $sequence[$i - 1]);

            if ($i === 0 || !$keyChanged) {
                $currentStatement[] = array_shift($statements[$key]);
            }

            if ($keyChanged) {
                $cypher[] = $this->formatLine($sequence[$i - 1], $currentStatement);
                $currentStatement = [array_shift($statements[$key])];
            }
        }

        if (count($currentStatement) > 0) {
            $cypher[] = $this->formatLine($key, $currentStatement);
        }

        if ($query->hasReturn()) {
            $cypher[] = $this->formatLine(Query::SEQUENCE_RETURN, $query->getReturn());
        }
        $cypher = implode("\n", $cypher);
        $cypher .= ';';

        return $cypher;
    }

    /**
     * Format a statement line
     *
     * @param string $key
     * @param array $statements
     *
     * @return string
     */
    protected function formatLine($key, array $statements)
    {
        return sprintf(
            '%s %s',
            $this->keys[(string) $key],
            implode(', ', $statements)
        );
    }
}
