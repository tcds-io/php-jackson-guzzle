<?php

namespace Test\Tcds\Io\Jackson\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Jackson\Guzzle\JacksonClient;
use Test\Tcds\Io\Jackson\Guzzle\Fixtures\Address;
use Test\Tcds\Io\Jackson\Guzzle\Fixtures\AddressCreated;

class JacksonClientTest extends TestCase
{
    private JacksonClient $client;
    private Client&MockObject $guzzle;

    protected function setUp(): void
    {
        $this->client = new JacksonClient(
            $this->guzzle = $this->createMock(Client::class),
        );
    }

    #[Test]
    public function parse_request_response_into_given_class(): void
    {
        $options = [
            RequestOptions::AUTH => ['arthur', 'dent'],
        ];
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', '/addresses', $options)
            ->willReturn(new Response(
                status: 200,
                headers: [],
                body: <<<JSON
                { "id": "aaa-bbb-ccc" }
                JSON,
            ));

        $response = $this->client->request(class: AddressCreated::class, method: 'POST', uri: '/addresses', options: $options);

        $this->assertEquals(new AddressCreated('aaa-bbb-ccc'), $response);
    }

    #[Test]
    public function parse_request_async_then_response_into_given_class(): void
    {
        $options = [
            RequestOptions::AUTH => ['arthur', 'dent'],
        ];
        $this->guzzle->expects($this->once())
            ->method('requestAsync')
            ->with('GET', '/addresses/aaa-bbb-ccc', $options)
            ->willReturn(
                Create::promiseFor(
                    new Response(
                        status: 200,
                        headers: [],
                        body: <<<JSON
                        { "id": "aaa-bbb-ccc", "street": "Ocean Avenue", "number": 42, "main":  true }
                        JSON,
                    ),
                ),
            );

        $response = null;

        $this->client
            ->requestAsync(
                class: Address::class,
                method: 'GET',
                uri: '/addresses/aaa-bbb-ccc',
                options: $options,
            )
            ->then(function ($res) use (&$response) {
                return $response = $res;
            })
            ->wait();

        $this->assertEquals(new Address(id: 'aaa-bbb-ccc', street: 'Ocean Avenue', number: 42, main: true), $response);
    }

    #[Test]
    public function parse_request_async_wait_response_into_given_class(): void
    {
        $options = [
            RequestOptions::AUTH => ['arthur', 'dent'],
        ];
        $this->guzzle->expects($this->once())
            ->method('requestAsync')
            ->with('POST', '/addresses/aaa-bbb-ccc', $options)
            ->willReturn(
                Create::promiseFor(
                    new Response(
                        status: 200,
                        headers: [],
                        body: <<<JSON
                        { "id": "aaa-bbb-ccc", "street": "Ocean Avenue", "number": 42, "main":  true }
                        JSON,
                    ),
                ),
            );

        $response = $this->client
            ->requestAsync(
                class: Address::class,
                method: 'POST',
                uri: '/addresses/aaa-bbb-ccc',
                options: $options,
            )
            ->wait();

        $this->assertEquals(new Address(id: 'aaa-bbb-ccc', street: 'Ocean Avenue', number: 42, main: true), $response);
    }

    #[Test]
    public function parse_get_response_into_given_class(): void
    {
        $options = [
            RequestOptions::AUTH => ['arthur', 'dent'],
        ];
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('GET', '/addresses/aaa-bbb-ccc', $options)
            ->willReturn(new Response(
                status: 200,
                headers: [],
                body: <<<JSON
                { "id": "aaa-bbb-ccc", "street": "Ocean Avenue", "number": 42, "main":  true }
                JSON,
            ));

        $response = $this->client->get(class: Address::class, uri: '/addresses/aaa-bbb-ccc', options: $options);

        $this->assertEquals(
            new Address(
                id: 'aaa-bbb-ccc',
                street: 'Ocean Avenue',
                number: 42,
                main: true,
            ),
            $response,
        );
    }

    #[Test]
    public function parse_get_async_response_into_given_class(): void
    {
        $options = [
            RequestOptions::AUTH => ['arthur', 'dent'],
        ];
        $this->guzzle->expects($this->once())
            ->method('requestAsync')
            ->with('GET', '/addresses/aaa-bbb-ccc', $options)
            ->willReturn(
                Create::promiseFor(
                    new Response(
                        status: 200,
                        headers: [],
                        body: <<<JSON
                        { "id": "aaa-bbb-ccc", "street": "Ocean Avenue", "number": 42, "main":  true }
                        JSON,
                    ),
                ),
            );

        $response = $this->client
            ->getAsync(class: Address::class, uri: '/addresses/aaa-bbb-ccc', options: $options)
            ->wait();

        $this->assertEquals(
            new Address(
                id: 'aaa-bbb-ccc',
                street: 'Ocean Avenue',
                number: 42,
                main: true,
            ),
            $response,
        );
    }

    #[Test]
    public function parse_post_response_into_given_class(): void
    {
        $options = [
            RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
            RequestOptions::JSON => ['street' => 'Ocean Avenue', 'number' => 42],
        ];
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('POST', '/addresses', $options)
            ->willReturn(new Response(
                status: 200,
                headers: [],
                body: <<<JSON
                { "id": "aaa-bbb-ccc" }
                JSON,
            ));

        $response = $this->client->post(class: AddressCreated::class, uri: '/addresses', options: $options);

        $this->assertEquals(new AddressCreated('aaa-bbb-ccc'), $response);
    }

    #[Test]
    public function parse_post_async_response_into_given_class(): void
    {
        $options = [
            RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
            RequestOptions::JSON => ['street' => 'Ocean Avenue', 'number' => 42],
        ];
        $this->guzzle->expects($this->once())
            ->method('requestAsync')
            ->with('POST', '/addresses', $options)
            ->willReturn(Create::promiseFor(new Response(
                status: 200,
                headers: [],
                body: <<<JSON
                { "id": "aaa-bbb-ccc" }
                JSON,
            )));

        $response = $this->client
            ->postAsync(class: AddressCreated::class, uri: '/addresses', options: $options)
            ->wait();

        $this->assertEquals(new AddressCreated('aaa-bbb-ccc'), $response);
    }

    #[Test]
    public function parse_put_response_into_given_class(): void
    {
        $options = [
            RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
            RequestOptions::JSON => ['street' => 'Ocean Avenue 2', 'number' => 44],
        ];
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('PUT', '/addresses/aaa-bbb-ccc', $options)
            ->willReturn(new Response(
                status: 200,
                headers: [],
                body: <<<JSON
                { "id": "aaa-bbb-ccc", "street": "Ocean Avenue 2", "number": 44, "main":  false }
                JSON,
            ));

        $response = $this->client->put(class: Address::class, uri: '/addresses/aaa-bbb-ccc', options: $options);

        $this->assertEquals(
            new Address(
                id: 'aaa-bbb-ccc',
                street: 'Ocean Avenue 2',
                number: 44,
                main: false,
            ),
            $response,
        );
    }

    #[Test]
    public function parse_put_async_response_into_given_class(): void
    {
        $options = [
            RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
            RequestOptions::JSON => ['street' => 'Ocean Avenue 2', 'number' => 44],
        ];
        $this->guzzle->expects($this->once())
            ->method('requestAsync')
            ->with('PUT', '/addresses/aaa-bbb-ccc', $options)
            ->willReturn(Create::promiseFor(new Response(
                status: 200,
                headers: [],
                body: <<<JSON
                { "id": "aaa-bbb-ccc", "street": "Ocean Avenue 2", "number": 44, "main":  false }
                JSON,
            )));

        $response = $this->client
            ->putAsync(class: Address::class, uri: '/addresses/aaa-bbb-ccc', options: $options)
            ->wait();

        $this->assertEquals(
            new Address(
                id: 'aaa-bbb-ccc',
                street: 'Ocean Avenue 2',
                number: 44,
                main: false,
            ),
            $response,
        );
    }

    #[Test]
    public function parse_patch_response_into_given_class(): void
    {
        $options = [
            RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
            RequestOptions::JSON => ['number' => 44],
        ];
        $this->guzzle->expects($this->once())
            ->method('request')
            ->with('PATCH', '/addresses/aaa-bbb-ccc', $options)
            ->willReturn(new Response(
                status: 200,
                headers: [],
                body: <<<JSON
                { "id": "aaa-bbb-ccc", "street": "Ocean Avenue", "number": 44, "main":  false }
                JSON,
            ));

        $response = $this->client->patch(class: Address::class, uri: '/addresses/aaa-bbb-ccc', options: $options);

        $this->assertEquals(
            new Address(
                id: 'aaa-bbb-ccc',
                street: 'Ocean Avenue',
                number: 44,
                main: false,
            ),
            $response,
        );
    }

    #[Test]
    public function parse_patch_async_response_into_given_class(): void
    {
        $options = [
            RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
            RequestOptions::JSON => ['number' => 44],
        ];
        $this->guzzle->expects($this->once())
            ->method('requestAsync')
            ->with('PATCH', '/addresses/aaa-bbb-ccc', $options)
            ->willReturn(Create::promiseFor(new Response(
                status: 200,
                headers: [],
                body: <<<JSON
                { "id": "aaa-bbb-ccc", "street": "Ocean Avenue", "number": 44, "main":  false }
                JSON,
            )));

        $response = $this->client
            ->patchAsync(class: Address::class, uri: '/addresses/aaa-bbb-ccc', options: $options)
            ->wait();

        $this->assertEquals(
            new Address(
                id: 'aaa-bbb-ccc',
                street: 'Ocean Avenue',
                number: 44,
                main: false,
            ),
            $response,
        );
    }
}
