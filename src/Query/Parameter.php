<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Query;

use Innmind\Neo4j\DBAL\Exception\DomainException;
use Innmind\Immutable\Str;

final class Parameter
{
    private string $key;
    private $value;

    public function __construct(string $key, $value)
    {
        if (Str::of($key)->empty()) {
            throw new DomainException;
        }

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
