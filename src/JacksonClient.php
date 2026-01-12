<?php

namespace Tcds\Io\Jackson\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\UriInterface;
use Tcds\Io\Jackson\Exception\JacksonException;
use Tcds\Io\Jackson\Exception\UnableToParseValue;
use Tcds\Io\Jackson\JsonObjectMapper;
use Tcds\Io\Jackson\ObjectMapper;

final readonly class JacksonClient
{
    public function __construct(
        public Client $guzzle,
        private ObjectMapper $mapper = new JsonObjectMapper(),
    ) {
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param string $method
     * @param string|UriInterface $uri
     * @param array<string, mixed> $options
     * @return T
     * @throws GuzzleException|JacksonException|UnableToParseValue
     */
    public function request(string $class, string $method, string|UriInterface $uri, array $options = [])
    {
        $response = $this->guzzle->request($method, $uri, $options);
        $content = $response->getBody()->getContents();

        /** @var T */
        return $this->mapper->readValue(asClassString($class), $content);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param string $method
     * @param string|UriInterface $uri
     * @param array<string, mixed> $options
     * @return JacksonPromise<T>
     * @throws GuzzleException
     */
    public function requestAsync(string $class, string $method, string|UriInterface $uri, array $options = []): JacksonPromise
    {
        $promise = $this->guzzle->requestAsync($method, $uri, $options);

        return new JacksonPromise($promise, $class, $this->mapper);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param string|UriInterface $uri
     * @param array<string, mixed> $options
     * @return T
     * @throws GuzzleException|JacksonException|UnableToParseValue
     */
    public function get(string $class, string|UriInterface $uri, array $options = [])
    {
        return $this->request(class: $class, method: 'GET', uri: $uri, options: $options);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param string|UriInterface $uri
     * @param array<string, mixed> $options
     * @return JacksonPromise<T>
     * @throws GuzzleException
     */
    public function getAsync(string $class, string|UriInterface $uri, array $options = []): JacksonPromise
    {
        return $this->requestAsync(class: $class, method: 'GET', uri: $uri, options: $options);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param string|UriInterface $uri
     * @param array<string, mixed> $options
     * @return T
     * @throws GuzzleException|JacksonException|UnableToParseValue
     */
    public function post(string $class, string|UriInterface $uri, array $options = [])
    {
        return $this->request(class: $class, method: 'POST', uri: $uri, options: $options);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param string|UriInterface $uri
     * @param array<string, mixed> $options
     * @return JacksonPromise<T>
     * @throws GuzzleException
     */
    public function postAsync(string $class, string|UriInterface $uri, array $options = []): JacksonPromise
    {
        return $this->requestAsync(class: $class, method: 'POST', uri: $uri, options: $options);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param string|UriInterface $uri
     * @param array<string, mixed> $options
     * @return T
     * @throws GuzzleException|JacksonException|UnableToParseValue
     */
    public function put(string $class, string|UriInterface $uri, array $options = [])
    {
        return $this->request(class: $class, method: 'PUT', uri: $uri, options: $options);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param string|UriInterface $uri
     * @param array<string, mixed> $options
     * @return JacksonPromise<T>
     * @throws GuzzleException
     */
    public function putAsync(string $class, string|UriInterface $uri, array $options = []): JacksonPromise
    {
        return $this->requestAsync(class: $class, method: 'PUT', uri: $uri, options: $options);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param string|UriInterface $uri
     * @param array<string, mixed> $options
     * @return T
     * @throws GuzzleException|JacksonException|UnableToParseValue
     */
    public function patch(string $class, string|UriInterface $uri, array $options = [])
    {
        $this->guzzle->patch($uri, $options);;
        return $this->request(class: $class, method: 'PATCH', uri: $uri, options: $options);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param string|UriInterface $uri
     * @param array<string, mixed> $options
     * @return JacksonPromise<T>
     * @throws GuzzleException
     */
    public function patchAsync(string $class, string|UriInterface $uri, array $options = []): JacksonPromise
    {
        return $this->requestAsync(class: $class, method: 'PATCH', uri: $uri, options: $options);
    }
}
