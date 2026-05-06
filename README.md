# PHP Jackson for Guzzle

[![PHP Tests](https://github.com/tcds-io/php-jackson-guzzle/actions/workflows/tests.yml/badge.svg)](https://github.com/tcds-io/php-jackson-guzzle/actions/workflows/tests.yml)

Guzzle integration for [tcds-io/php-jackson](https://github.com/tcds-io/php-jackson), a type-safe object mapper inspired by Jackson (Java).

This package provides a typed HTTP client built on top of Guzzle that automatically maps request DTOs into Guzzle options and JSON responses into strongly typed PHP objects, synchronously and asynchronously.

---

## Features

- Typed object mapping for HTTP responses
- Request DTO mapping for query params, JSON bodies, and form params
- Full async support with typed promises
- Works with immutable and readonly DTOs
- Keeps the native Guzzle client accessible
- Built on top of php-jackson for consistent serialization rules

---

## Installation

```bash
composer require tcds-io/php-jackson-guzzle
```

---

## How it works

1. Create a `JacksonClient` with a native Guzzle client.
2. Pass the expected response DTO class to `get`, `post`, `put`, `patch`, or `request`.
3. Optionally pass request DTOs as query params, JSON body, or form params.
4. Guzzle sends the HTTP request.
5. PHP-Jackson deserializes the JSON response into your expected DTO.

---

## Basic usage

```php
use GuzzleHttp\Client;
use Tcds\Io\Jackson\Guzzle\JacksonClient;

$client = new JacksonClient(
    new Client([
        'base_uri' => 'https://api.example.com',
    ]),
);
```

### Typed GET

```php
readonly class Address
{
    public function __construct(
        public string $id,
        public string $street,
        public int $number,
        public bool $main,
    ) {}
}

$address = $client->get(Address::class, '/addresses/aaa');
```

### Typed POST with a JSON body

```php
readonly class CreateAddress
{
    public function __construct(
        public string $street,
        public int $number,
    ) {}
}

readonly class AddressCreated
{
    public function __construct(public string $id) {}
}

$created = $client->post(
    class: AddressCreated::class,
    uri: '/addresses',
    jsonBody: new CreateAddress(street: 'Ocean Avenue', number: 42),
);
```

You can still pass native Guzzle options when needed:

```php
use GuzzleHttp\RequestOptions;

$created = $client->post(
    class: AddressCreated::class,
    uri: '/addresses',
    options: [
        RequestOptions::HEADERS => ['X-Request-ID' => 'abc-123'],
    ],
    jsonBody: new CreateAddress(street: 'Ocean Avenue', number: 42),
);
```

If an option is already present in `options`, the client leaves it untouched.

---

## Request data

`JacksonClient` can serialize DTOs into the common Guzzle request option buckets:

```php
$response = $client->request(
    class: AddressCreated::class,
    method: 'POST',
    uri: '/addresses',
    queryParams: new SearchAddress(street: 'Ocean Avenue'),
    jsonBody: new CreateAddress(street: 'Ocean Avenue', number: 42),
    formParams: new AuditFields(source: 'backoffice'),
);
```

- `queryParams` maps to `RequestOptions::QUERY`
- `jsonBody` maps to `RequestOptions::JSON`
- `formParams` maps to `RequestOptions::FORM_PARAMS`

Pass `null` to omit an option. Empty arrays are treated as intentional payloads and are still passed to Guzzle.

---

## Async

```php
$address = $client
    ->getAsync(Address::class, '/addresses/aaa')
    ->wait();
```

The async methods return `JacksonPromise`, which decorates Guzzle's promise and maps fulfilled `ResponseInterface` values into your expected DTO.

```php
$client
    ->postAsync(
        class: AddressCreated::class,
        uri: '/addresses',
        jsonBody: new CreateAddress(street: 'Ocean Avenue', number: 42),
    )
    ->then(fn(AddressCreated $created) => $created->id)
    ->wait();
```

---

## Custom mappers

Pass php-jackson type mappers to the `JacksonClient` constructor when a type needs custom read or write behavior.

```php
use GuzzleHttp\Client;
use Tcds\Io\Jackson\Guzzle\JacksonClient;

$client = new JacksonClient(
    guzzle: new Client([
        'base_uri' => 'https://api.example.com',
    ]),
    typeMappers: [
        User::class => [
            'reader' => fn(array $data) => new User(
                name: $data['name'],
                lastName: $data['surname'],
            ),
            'writer' => fn(User $user) => [
                'name' => $user->name,
                'surname' => $user->lastName,
            ],
        ],
    ],
);
```

Please refer to the core mapper documentation for additional configuration options.

---

## Development

```bash
composer install
vendor/bin/phpunit --testdox
```

---

## Related packages

- Core mapper: https://github.com/tcds-io/php-jackson
- Symfony integration: https://github.com/tcds-io/php-jackson-symfony
- Laravel integration: https://github.com/tcds-io/php-jackson-laravel
