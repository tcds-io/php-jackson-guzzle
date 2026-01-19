<?php

namespace Tcds\Io\Jackson\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;
use Tcds\Io\Jackson\ArrayObjectMapper;
use Tcds\Io\Jackson\Exception\JacksonException;
use Tcds\Io\Jackson\JsonObjectMapper;
use Tcds\Io\Jackson\ObjectMapper;

/**
 * @phpstan-import-type TypeMappers from ObjectMapper
 */
final readonly class JacksonClient
{
    private JsonObjectMapper $jsonMapper;
    private ArrayObjectMapper $arrayMapper;

    /**
     * @param TypeMappers $typeMappers
     */
    public function __construct(public Client $guzzle, array $typeMappers = [])
    {
        $this->jsonMapper = new JsonObjectMapper(typeMappers: $typeMappers);
        $this->arrayMapper = new ArrayObjectMapper(typeMappers: $typeMappers);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param array<string, mixed> $options
     * @return T
     * @throws GuzzleException|JacksonException
     */
    public function request(
        string $class,
        string $method,
        string|UriInterface $uri,
        array $options = [],
        mixed $queryParams = null,
        mixed $jsonBody = null,
        mixed $formParams = null,
    ) {
        $options = $this->setupOptions($options, $queryParams, $jsonBody, $formParams);
        $response = $this->guzzle->request($method, $uri, $options);
        $content = $response->getBody()->getContents();

        /** @var T */
        return $this->jsonMapper->readValue(asClassString($class), $content);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param array<string, mixed> $options
     * @return JacksonPromise<T>
     * @throws GuzzleException|JacksonException
     */
    public function requestAsync(
        string $class,
        string $method,
        string|UriInterface $uri,
        array $options = [],
        mixed $queryParams = null,
        mixed $jsonBody = null,
        mixed $formParams = null,
    ): JacksonPromise {
        $options = $this->setupOptions($options, $queryParams, $jsonBody, $formParams);
        $promise = $this->guzzle->requestAsync($method, $uri, $options);

        return new JacksonPromise($promise, $class, $this->jsonMapper);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param array<string, mixed> $options
     * @return T
     * @throws GuzzleException|JacksonException
     */
    public function get(string $class, string|UriInterface $uri, array $options = [], mixed $queryParams = null)
    {
        return $this->request(class: $class, method: 'GET', uri: $uri, options: $options, queryParams: $queryParams);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param array<string, mixed> $options
     * @return JacksonPromise<T>
     * @throws GuzzleException|JacksonException
     */
    public function getAsync(string $class, string|UriInterface $uri, array $options = [], mixed $queryParams = null): JacksonPromise
    {
        return $this->requestAsync(class: $class, method: 'GET', uri: $uri, options: $options, queryParams: $queryParams);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param array<string, mixed> $options
     * @return T
     * @throws GuzzleException|JacksonException
     */
    public function post(
        string $class,
        string|UriInterface $uri,
        array $options = [],
        mixed $queryParams = null,
        mixed $jsonBody = null,
        mixed $formParams = null,
    ) {
        return $this->request(
            class: $class,
            method: 'POST',
            uri: $uri,
            options: $options,
            queryParams: $queryParams,
            jsonBody: $jsonBody,
            formParams: $formParams,
        );
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param array<string, mixed> $options
     * @return JacksonPromise<T>
     * @throws GuzzleException|JacksonException
     */
    public function postAsync(
        string $class,
        string|UriInterface $uri,
        array $options = [],
        mixed $queryParams = null,
        mixed $jsonBody = null,
        mixed $formParams = null,
    ): JacksonPromise {
        return $this->requestAsync(
            class: $class,
            method: 'POST',
            uri: $uri,
            options: $options,
            queryParams: $queryParams,
            jsonBody: $jsonBody,
            formParams: $formParams,
        );
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param array<string, mixed> $options
     * @return T
     * @throws GuzzleException|JacksonException
     */
    public function put(
        string $class,
        string|UriInterface $uri,
        array $options = [],
        mixed $queryParams = null,
        mixed $jsonBody = null,
        mixed $formParams = null,
    ) {
        return $this->request(
            class: $class,
            method: 'PUT',
            uri: $uri,
            options: $options,
            queryParams: $queryParams,
            jsonBody: $jsonBody,
            formParams: $formParams,
        );
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param array<string, mixed> $options
     * @return JacksonPromise<T>
     * @throws GuzzleException|JacksonException
     */
    public function putAsync(
        string $class,
        string|UriInterface $uri,
        array $options = [],
        mixed $queryParams = null,
        mixed $jsonBody = null,
        mixed $formParams = null,
    ): JacksonPromise {
        return $this->requestAsync(
            class: $class,
            method: 'PUT',
            uri: $uri,
            options: $options,
            queryParams: $queryParams,
            jsonBody: $jsonBody,
            formParams: $formParams,
        );
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param array<string, mixed> $options
     * @return T
     * @throws GuzzleException|JacksonException
     */
    public function patch(
        string $class,
        string|UriInterface $uri,
        array $options = [],
        mixed $queryParams = null,
        mixed $jsonBody = null,
        mixed $formParams = null,
    ) {
        return $this->request(
            class: $class,
            method: 'PATCH',
            uri: $uri,
            options: $options,
            queryParams: $queryParams,
            jsonBody: $jsonBody,
            formParams: $formParams,
        );
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     * @param array<string, mixed> $options
     * @return JacksonPromise<T>
     * @throws GuzzleException|JacksonException
     */
    public function patchAsync(
        string $class,
        string|UriInterface $uri,
        array $options = [],
        mixed $queryParams = null,
        mixed $jsonBody = null,
        mixed $formParams = null,
    ): JacksonPromise {
        return $this->requestAsync(
            class: $class,
            method: 'PATCH',
            uri: $uri,
            options: $options,
            queryParams: $queryParams,
            jsonBody: $jsonBody,
            formParams: $formParams,
        );
    }

    /**
     * @throws JacksonException
     */
    private function setupOptions(
        array $options,
        mixed $queryParams,
        mixed $jsonBody,
        mixed $formParams,
    ): array {
        if ($formParams != null && !isset($options[RequestOptions::FORM_PARAMS])) {
            $options[RequestOptions::FORM_PARAMS] = $this->arrayMapper->writeValue($formParams);
        }

        if ($jsonBody != null && !isset($options[RequestOptions::JSON])) {
            $options[RequestOptions::JSON] = $this->arrayMapper->writeValue($jsonBody);
        }

        if ($queryParams != null && !isset($options[RequestOptions::QUERY])) {
            $options[RequestOptions::QUERY] = $this->arrayMapper->writeValue($queryParams);
        }

        return $options;
    }
}
