<?php

namespace Innmind\Neo4j\DBAL;

class WhereExpr
{
    const CONDITION_OR = 'OR';
    const CONDITION_OR_NOT = 'OR NOT';
    const CONDITION_XOR = 'XOR';
    const CONDITION_AND = 'AND';
    const CONDITION_AND_NOT = 'AND NOT';

    protected $conditions = [];
    protected $sequence = [];

    /**
     * Add a OR condition
     *
     * @param string|WhereExpr $cond
     *
     * @return WhereExpr self
     */
    public function orWhere($cond)
    {
        $this->conditions[] = $cond;
        $this->sequence[] = self::CONDITION_OR;

        return $this;
    }

    /**
     * Add a OR NOT condition
     *
     * @param string|WhereExpr $cond
     *
     * @return WhereExpr self
     */
    public function orNotWhere($cond)
    {
        $this->conditions[] = $cond;
        $this->sequence[] = self::CONDITION_OR_NOT;

        return $this;
    }

    /**
     * Add a XOR condition
     *
     * @param string|WhereExpr $cond
     *
     * @return WhereExpr self
     */
    public function xorWhere($cond)
    {
        $this->conditions[] = $cond;
        $this->sequence[] = self::CONDITION_XOR;

        return $this;
    }

    /**
     * Add a AND condition
     *
     * @param string|WhereExpr $cond
     *
     * @return WhereExpr self
     */
    public function andWhere($cond)
    {
        $this->conditions[] = $cond;
        $this->sequence[] = self::CONDITION_AND;

        return $this;
    }

    /**
     * Add a AND NOT condition
     *
     * @param string|WhereExpr $cond
     *
     * @return WhereExpr self
     */
    public function andNotWhere($cond)
    {
        $this->conditions[] = $cond;
        $this->sequence[] = self::CONDITION_AND_NOT;

        return $this;
    }

    /**
     * Return string representation of the where clause
     *
     * @return string
     */
    public function __toString()
    {
        $string = '';

        if (count($this->conditions) > 0) {
            $string = sprintf('(%s)', (string) $this->conditions[0]);
        }

        for ($i = 1, $l = count($this->conditions); $i < $l; $i++) {
            $current = $this->conditions[$i];

            $string .= sprintf(' %s (%s)', $this->sequence[$i], $current);
        }

        return $string;
    }
}
