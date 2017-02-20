<?php
declare(strict_types = 1);

namespace Innmind\Neo4j\DBAL\Transport;

use Innmind\Neo4j\DBAL\{
    TransportInterface,
    QueryInterface,
    ResultInterface,
    Result,
    Server,
    Authentication,
    Events,
    Event\PreQueryEvent,
    Event\PostQueryEvent,
    Translator\HttpTranslator,
    Exception\ServerDownException,
    Exception\QueryException
};
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Http implements TransportInterface
{
    private $translator;
    private $dispatcher;
    private $http;

    public function __construct(
        HttpTranslator $translator,
        EventDispatcherInterface $dispatcher,
        Server $server,
        Authentication $authentication,
        int $timeout = 60
    ) {
        $this->translator = $translator;
        $this->dispatcher = $dispatcher;
        $this->http = new Client([
            'base_uri' => (string) $server,
            'timeout' => $timeout,
            'headers' => [
                'Authorization' => base64_encode(sprintf(
                    '%s:%s',
                    $authentication->user(),
                    $authentication->password()
                )),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(QueryInterface $query): ResultInterface
    {
        $this->dispatcher->dispatch(
            Events::PRE_QUERY,
            new PreQueryEvent($query)
        );
        $response = $this->http->send(
            $this->translator->translate($query)
        );

        if (!$this->isSuccessful($response)) {
            throw QueryException::failed($query, $response);
        }

        $response = json_decode((string) $response->getBody(), true);
        $result = Result::fromRaw($response['results'][0] ?? []);
        $this->dispatcher->dispatch(
            Events::POST_QUERY,
            new PostQueryEvent($query, $result)
        );

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function ping(): TransportInterface
    {
        try {
            $code = $this
                ->http
                ->options('')
                ->getStatusCode();
        } catch (\Exception $e) {
            throw new ServerDownException;
        }

        if ($code >= 200 && $code < 300) {
            return $this;
        }

        throw new ServerDownException;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * Check if the response is successful
     *
     * @param ResponseInterface $response
     *
     * @return bool
     */
    private function isSuccessful(ResponseInterface $response): bool
    {
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $json = json_decode((string) $response->getBody(), true);

        return count($json['errors']) === 0;
    }
}
