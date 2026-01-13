# PHP Jackson for Guzzle

Guzzle integration for
[tcds-io/php-jackson](https://github.com/tcds-io/php-jackson), a
type-safe object mapper inspired by Jackson (Java).

This package provides a **typed HTTP client** built on top of Guzzle
that automatically maps JSON responses into strongly-typed PHP objects
--- both synchronously and asynchronously.

------------------------------------------------------------------------

## ✨ Features

-   Typed object mapping for HTTP responses
-   Full async support with typed promises
-   Works seamlessly with immutable and readonly DTOs
-   Keeps the native Guzzle client fully accessible
-   Built on top of php-jackson for consistent serialization rules

------------------------------------------------------------------------

## 🚀 Installation

``` bash
composer require tcds-io/php-jackson-guzzle
```

------------------------------------------------------------------------

## ⚙️ How it works

1.  You specify the DTO class and your guzzle request information.
2.  JacksonClient sends HTTP requests via Guzzle.
3.  JSON responses are automatically deserialized into your DTOs.
4.  Async calls return typed promises.

------------------------------------------------------------------------

## 🧩 Basic Usage

``` php
use Jackson\JacksonClient;
use GuzzleHttp\Client;

$client = new JacksonClient(
    new Client([
        'base_uri' => 'https://api.example.com',
    ]),
);
```

### Typed GET

``` php
// Dto
readonly class Address
{
    public function __construct(
        public string $id,
        public string $street,
        public int $number,
        public bool $main,
    ) {}
}

// Request
$address = $client->get(Address::class, '/addresses/aaa');
```

### Typed POST

``` php
// Dto
readonly class AddressCreated
{
    public function __construct(public string $id) {}
}

// Request
$created = $client->post(AddressCreated::class, '/addresses', [
    RequestOptions::JSON => ['street' => 'Ocean Avenue', 'number' => 42],
]);
```

------------------------------------------------------------------------

## ⚡ Async

``` php
$address = $client
    ->getAsync(Address::class, '/addresses/aaa')
    ->wait();
```

------------------------------------------------------------------------

## 🛠 Configuring Serializable Objects

While most DTOs work out of the box, some require custom deserialization rules.

JacksonClient exposes the underlying object mapper so you can define custom type mappers for full control over how JSON is converted into objects.

``` php
use Jackson\JacksonClient;
use GuzzleHttp\Client;

$client = new JacksonClient(
    guzzle: new Client([
        'base_uri' => 'https://api.example.com',
    ]),
    mapper: new JsonObjectMapper(
        typeMappers: [
            User::class => fn(array $data) => new User(
                name: $data['name'],
                lastName: $data['surname'],
            ),
        ],
    ),
);
```
Please refer to the core mapper documentation for additional configuration options.

------------------------------------------------------------------------

## 📦 Related Packages

-   Core mapper: https://github.com/tcds-io/php-jackson
-   Symfony integration: https://github.com/tcds-io/php-jackson-symfony
-   Laravel integration: https://github.com/tcds-io/php-jackson-laravel
