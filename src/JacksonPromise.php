<?php

namespace Tcds\Io\Jackson\Guzzle;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Tcds\Io\Jackson\Exception\JacksonException;
use Tcds\Io\Jackson\ObjectMapper;

/**
 * @template T
 */
readonly class JacksonPromise implements PromiseInterface
{
    public function __construct(
        private PromiseInterface $original,
        private string $class,
        private ObjectMapper $mapper,
    ) {
    }

    /**
     * @template R
     * @param (Closure (T): R)|null $onFulfilled
     * @param Closure|null $onRejected
     * @return PromiseInterface
     */
    public function then(?callable $onFulfilled = null, ?callable $onRejected = null): PromiseInterface
    {
        return $this->original
            ->then(onFulfilled: $this->parse(...), onRejected: $onRejected)
            ->then($onFulfilled);
    }

    public function otherwise(callable $onRejected): PromiseInterface
    {
        return $this->original->otherwise($onRejected);
    }

    public function getState(): string
    {
        return $this->original->getState();
    }

    public function resolve($value): void
    {
        $this->original->resolve($value);
    }

    public function reject($reason): void
    {
        $this->original->reject($reason);
    }

    public function cancel(): void
    {
        $this->original->cancel();
    }

    /**
     * @return T
     * @throws JacksonException
     */
    public function wait(bool $unwrap = true)
    {
        $response = $this->original->wait($unwrap);

        return $response instanceof ResponseInterface
            ? $this->parse(response: $response)
            : $response;
    }

    /**
     * @return T
     * @throws JacksonException
     */
    private function parse(ResponseInterface $response)
    {
        return $this->mapper->readValue(
            type: asClassString($this->class),
            value: $response->getBody()->getContents(),
        );
    }
}
