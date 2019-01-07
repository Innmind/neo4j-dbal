<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

use Innmind\Neo4j\DBAL\Exception\DomainException;
use Innmind\Immutable\Str;

final class Authentication
{
    private $user;
    private $password;

    public function __construct(string $user = null, string $password = null)
    {
        $user = $user ?? 'neo4j';
        $password = $password ?? 'neo4j';

        if (Str::of($user)->empty() || Str::of($password)->empty()) {
            throw new DomainException;
        }

        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Return the username
     *
     * @return string
     */
    public function user(): string
    {
        return $this->user;
    }

    /**
     * Return the password
     *
     * @return string
     */
    public function password(): string
    {
        return $this->password;
    }
}
