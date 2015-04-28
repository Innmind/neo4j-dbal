<?php

namespace Innmind\Neo4j\DBAL;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Innmind\Neo4j\DBAL\Event\ApiResponseEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Client;

class Connection
{
    protected $http;
    protected $dispatcher;

    public function __construct(array $params, EventDispatcherInterface $dispatcher)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'scheme' => 'http',
            'host' => 'localhost',
            'port' => 7474,
            'timeout' => 60
        ]);
        $resolver->setDefined(['username', 'password']);
        $resolver->setRequired(['scheme', 'host', 'port']);
        $resolver->setAllowedTypes('scheme', 'string');
        $resolver->setAllowedTypes('host', 'string');
        $resolver->setAllowedTypes('port', ['int', 'null']);
        $resolver->setAllowedTypes('username', 'string');
        $resolver->setAllowedTypes('password', 'string');
        $resolver->setAllowedTypes('timeout', 'int');
        $resolver->setAllowedValues('scheme', ['http', 'https']);
        $resolver->setNormalizer('port', function ($options, $value) {
            if (in_array($value, [80, 0, null], true)) {
                return '';
            }

            if ($value === 443 && $options['scheme'] === 'https') {
                return '';
            }

            return sprintf(':%s', $value);
        });
        $params = $resolver->resolve($params);

        $this->dispatcher = $dispatcher;

        $headers = [];

        if (isset($params['username']) && isset($params['password'])) {
            $headers['Authorization'] = sprintf(
                'Basic %s',
                base64_encode(sprintf(
                    '%s:%s',
                    $params['username'],
                    $params['password']
                ))
            );
        }

        $this->http = new Client([
            'base_url' => sprintf(
                '%s://%s%s/db/data/',
                $params['scheme'],
                $params['host'],
                $params['port']
            ),
            'defaults' => [
                'headers' => $headers,
                'timeout' => $params['timeout'],
            ],
        ]);

        $this->configureListeners();
    }

    /**
     * Return the event dispatcher associated with this connection
     *
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Dispatch an event each time a http request is completed
     */
    protected function configureListeners()
    {
        $this->http->getEmitter()->on('complete', function (CompleteEvent $event) {
            $this->dispatcher->dispatch(Events::API_RESPONSE, new ApiResponseEvent($event->getResponse()));
        });
    }
}
