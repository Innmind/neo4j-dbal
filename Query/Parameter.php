<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Query;

class Parameter
{
    private $key;
    private $value;

    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function value()
    {
        return $this->value;
    }
}
