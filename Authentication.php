<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL;

class Authentication
{
    private $user;
    private $password;

    public function __construct(string $user, string $password)
    {
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
