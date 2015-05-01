<?php

namespace Innmind\Neo4j\DBAL;

class Query
{
    const SEQUENCE_MATCH = 'match';
    const SEQUENCE_OPTIONAL_MATCH = 'optionalMatch';
    const SEQUENCE_WHERE = 'where';
    const SEQUENCE_CREATE = 'create';
    const SEQUENCE_MERGE = 'merge';
    const SEQUENCE_ON_MATCH = 'onMatch';
    const SEQUENCE_ON_CREATE = 'onCreate';
    const SEQUENCE_SET = 'set';
    const SEQUENCE_DELETE = 'delete';
    const SEQUENCE_REMOVE = 'remove';
    const SEQUENCE_RETURN = 'return';
    const SEQUENCE_ORDER_BY = 'orderBy';
    const SEQUENCE_LIMIT = 'limit';
    const SEQUENCE_SKIP = 'skip';
    const SEQUENCE_WITH = 'with';
    const SEQUENCE_UNWIND = 'unwind';
    const SEQUENCE_UNION = 'union';
    const SEQUENCE_USING = 'using';

    protected $sequence = [];
    protected $match = [];
    protected $optionalMatch = [];
    protected $where = [];
    protected $create = [];
    protected $merge = [];
    protected $onMatch = [];
    protected $onCreate = [];
    protected $set = [];
    protected $delete = [];
    protected $remove = [];
    protected $return = [];
    protected $orderBy = [];
    protected $limit = [];
    protected $skip = [];
    protected $with = [];
    protected $unwind = [];
    protected $union = [];
    protected $using = [];
    protected $parameters = [];

    /**
     * Set a match statement
     *
     * @param string $match
     *
     * @return Query self
     */
    public function match($match)
    {
        $this->sequence[] = self::SEQUENCE_MATCH;
        $this->match[] = (string) $match;

        return $this;
    }

    /**
     * Return the match statements
     *
     * @return array
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set an optional match
     *
     * @param string $opt
     *
     * @return Query self
     */
    public function optionalMatch($opt)
    {
        $this->sequence[] = self::SEQUENCE_OPTIONAL_MATCH;
        $this->optionalMatch[] = (string) $opt;

        return $this;
    }

    /**
     * Return the optional match statements
     *
     * @return array
     */
    public function getOptionalMatch()
    {
        return $this->optionalMatch;
    }

    /**
     * Set the where clause
     *
     * @param string|WhereExpr $where
     *
     * @return Query self
     */
    public function where($where)
    {
        $this->sequence[] = self::SEQUENCE_WHERE;
        $this->where[] = $where;

        return $this;
    }

    /**
     * Create a where expression
     *
     * @return WhereExpr
     */
    public function createWhereExpr()
    {
        return new WhereExpr;
    }

    /**
     * Return where statements
     *
     * @return array
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * Set the create statement
     *
     * @param string $create
     *
     * @return Query self
     */
    public function create($create)
    {
        $this->sequence[] = self::SEQUENCE_CREATE;
        $this->create[] = (string) $create;

        return $this;
    }

    /**
     * Return the create statements
     *
     * @return array
     */
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * Set the merge statement
     *
     * @param string $merge
     *
     * @return Query self
     */
    public function merge($merge)
    {
        $this->sequence[] = self::SEQUENCE_MERGE;
        $this->merge[] = (string) $merge;

        return $this;
    }

    /**
     * Return the merge statements
     *
     * @return array
     */
    public function getMerge()
    {
        return $this->merge;
    }

    /**
     * Set the "ON MATCH" statement
     *
     * @param string $onMatch
     *
     * @return Query self
     */
    public function onMatch($onMatch)
    {
        $this->sequence[] = self::SEQUENCE_ON_MATCH;
        $this->onMatch[] = (string) $onMatch;

        return $this;
    }

    /**
     * Return "ON MATCH" statements
     *
     * @return array
     */
    public function getOnMatch()
    {
        return $this->onMatch;
    }

    /**
     * Set the "ON CREATE" statement
     *
     * @param string $onCreate
     *
     * @return Query self
     */
    public function onCreate($onCreate)
    {
        $this->sequence[] = self::SEQUENCE_ON_CREATE;
        $this->onCreate[] = (string) $onCreate;

        return $this;
    }

    /**
     * Return "ON CREATE" statements
     *
     * @return array
     */
    public function getOnCreate()
    {
        return $this->onCreate;
    }

    /**
     * Set a "SET" statement
     *
     * @param string $set
     *
     * @return Query self
     */
    public function set($set)
    {
        $this->sequence[] = self::SEQUENCE_SET;
        $this->set[] = (string) $set;

        return $this;
    }

    /**
     * Return set statements
     *
     * @return array
     */
    public function getSet()
    {
        return $this->set;
    }

    /**
     * Set a delete statement
     *
     * @param string $delete
     *
     * @return Query self
     */
    public function delete($delete)
    {
        $this->sequence[] = self::SEQUENCE_DELETE;
        $this->delete[] = (string) $delete;

        return $this;
    }

    /**
     * Return delete statements
     *
     * @return array
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * Set a remove statement
     *
     * @param string $remove
     *
     * @return Query self
     */
    public function remove($remove)
    {
        $this->sequence[] = self::SEQUENCE_REMOVE;
        $this->remove[] = (string) $remove;

        return $this;
    }

    /**
     * Return remove statements
     *
     * @return array
     */
    public function getRemove()
    {
        return $this->remove;
    }

    /**
     * Set the return statement
     *
     * @param string $return
     *
     * @return Query self
     */
    public function setReturn($return)
    {
        $this->sequence[] = self::SEQUENCE_RETURN;
        $this->return[] = (string) $return;

        return $this;
    }

    /**
     * Check if the query has a return statement
     *
     * @return bool
     */
    public function hasReturn()
    {
        return count($this->return) > 0;
    }

    /**
     * Return the return statement
     *
     * @return array
     */
    public function getReturn()
    {
        return $this->return;
    }

    /**
     * Set ORDER BY statement
     *
     * @param string $orderBy
     *
     * @return Query self
     */
    public function orderBy($orderBy)
    {
        $this->sequence[] = self::SEQUENCE_ORDER_BY;
        $this->orderBy[] = (string) $orderBy;

        return $this;
    }

    /**
     * Return the ORDER BY statements
     *
     * @return array
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set LIMIT statement
     *
     * @param string $limit
     *
     * @return Query self
     */
    public function limit($limit)
    {
        $this->sequence[] = self::SEQUENCE_LIMIT;
        $this->limit[] = (string) $limit;

        return $this;
    }

    /**
     * Return the LIMIT statements
     *
     * @return array
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set SKIP statement
     *
     * @param string $skip
     *
     * @return Query self
     */
    public function skip($skip)
    {
        $this->sequence[] = self::SEQUENCE_SKIP;
        $this->skip[] = (string) $skip;

        return $this;
    }

    /**
     * Return the SKIP statements
     *
     * @return array
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * Set WITH statement
     *
     * @param string $with
     *
     * @return Query self
     */
    public function with($with)
    {
        $this->sequence[] = self::SEQUENCE_WITH;
        $this->with[] = (string) $with;

        return $this;
    }

    /**
     * Return the WITH statements
     *
     * @return array
     */
    public function getWith()
    {
        return $this->with;
    }

    /**
     * Set UNWIND statement
     *
     * @param string $unwind
     *
     * @return Query self
     */
    public function unwind($unwind)
    {
        $this->sequence[] = self::SEQUENCE_UNWIND;
        $this->unwind[] = (string) $unwind;

        return $this;
    }

    /**
     * Return the UNWIND statements
     *
     * @return array
     */
    public function getUnwind()
    {
        return $this->unwind;
    }

    /**
     * Set UNION statement
     *
     * @param string $union
     *
     * @return Query self
     */
    public function union($union)
    {
        $this->sequence[] = self::SEQUENCE_UNION;
        $this->union[] = (string) $union;

        return $this;
    }

    /**
     * Return the UNION statements
     *
     * @return array
     */
    public function getUnion()
    {
        return $this->union;
    }

    /**
     * Set USING statement
     *
     * @param string $using
     *
     * @return Query self
     */
    public function using($using)
    {
        $this->sequence[] = self::SEQUENCE_USING;
        $this->using[] = (string) $using;

        return $this;
    }

    /**
     * Return the USING statements
     *
     * @return array
     */
    public function getUsing()
    {
        return $this->using;
    }

    /**
     * Return the the sequence keys in the order it has been
     * specified
     *
     * @return array
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Add a parameter to the query
     *
     * @param string name
     * @param mixed $value
     *
     * @return Query self
     */
    public function addParameter($name, $value)
    {
        $this->parameters[(string) $name] = $value;

        return $this;
    }

    /**
     * Check if parameters has been set
     *
     * @return bool
     */
    public function hasParameters()
    {
        return (bool) count($this->parameters);
    }

    /**
     * Check if the given parameter is set
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Return all the parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
