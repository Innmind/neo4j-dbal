<?php

namespace Innmind\Neo4j\DBAL\EventListener;

use Innmind\Neo4j\DBAL\Event\ApiResponseEvent;
use Innmind\Neo4j\DBAL\Exception\PasswordChangeRequiredException;
use GuzzleHttp\Message\ResponseInterface;

class ResponseListener
{
    public function handle(ApiResponseEvent $event)
    {
        $response = $event->getResponse();

        $this->verifyCredentials($response);
    }

    protected function verifyCredentials(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 401) {
            throw new \InvalidArgumentException(sprintf('Invalid Neo4j credentials'));
        }

        if ($response->getStatusCode() === 200) {
            $content = $response->json();

            if (isset($content['password_change_required']) && $content['password_change_required'] === true) {
                throw new PasswordChangeRequiredException(sprintf(
                    'A new password is required for the user "%s"',
                    $content['username']
                ));
            }
        }
    }
}
