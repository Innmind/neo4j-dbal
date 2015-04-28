<?php

namespace Innmind\Neo4j\DBAL;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Innmind\Neo4j\DBAL\EventListener\ResponseListener;

class ConnectionFactory
{
    public static function make(array $params = [], EventDispatcherInterface $dispatcher = null)
    {
        if ($dispatcher === null) {
            $dispatcher = new EventDispatcher();
        }

        $conn = new Connection($params, $dispatcher);

        if (!($dispatcher instanceof ImmutableEventDispatcher)) {
            $listener = new ResponseListener();
            $dispatcher->addListener(Events::API_RESPONSE, [$listener, 'handle']);
        }

        return $conn;
    }
}
