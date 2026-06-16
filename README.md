# Laravel Response Compression

[![Latest Stable Version](https://poser.pugx.org/chr15k/laravel-response-compression/v)](https://packagist.org/packages/chr15k/laravel-response-compression) [![Total Downloads](https://poser.pugx.org/chr15k/laravel-response-compression/downloads)](https://packagist.org/packages/chr15k/laravel-response-compression) [![Latest Unstable Version](https://poser.pugx.org/chr15k/laravel-response-compression/v/unstable)](https://packagist.org/packages/chr15k/laravel-response-compression) [![License](https://poser.pugx.org/chr15k/laravel-response-compression/license)](https://packagist.org/packages/chr15k/laravel-response-compression) [![PHP Version Require](https://poser.pugx.org/chr15k/laravel-response-compression/require/php)](https://packagist.org/packages/chr15k/laravel-response-compression)

Boost your Laravel application's performance by optimizing HTTP responses with middleware for compression.

---

## Installation

Install the package via Composer:

```bash
composer require chr15k/laravel-response-compression
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Chr15k\ResponseCompression\ResponseCompressionServiceProvider"
```

---

## Middleware Overview

This package provides the following middleware:

#### Compression Middleware

Applies **Gzip** or **Brotli** compression to HTTP responses based on client support. This reduces the size of the response payload and enhances load times.

**Ideal For**: Large JSON responses, static files, or data-intensive endpoints.

> [!NOTE]
> To use Brotli effectively, ensure that the Brotli PHP extension is properly installed.
> https://pecl.php.net/package/brotli

> [!WARNING]
> When using Brotli, a client-side decoding error may occur with non-secure connections, as modern browsers generally support Brotli compression only over HTTPS.

---

## Setup

### Register Middleware

#### Global Middleware

Apply the middleware globally to all requests:

```php
// bootstrap/app.php

->withMiddleware(function (Middleware $middleware) {
    ...
    $middleware->web(append: [
        ...
        \Chr15k\ResponseCompression\Middleware\CompressResponse::class,
    ]);
})
```

#### Route Middleware

Alternatively, register it as route middleware for selective application:

```php
use Chr15k\ResponseCompression\Middleware\CompressResponse;

Route::get('/profile', function () {
    // ...
})->middleware(CompressResponse::class);
```

---

## Config

```php
/**
 * Enable or disable the response compression.
 */
'enabled' => env('RESPONSE_COMPRESSION_ENABLED', true),

/**
 * The compression algorithm to use. Can be either 'gzip' or 'br'.
 */
'algorithm' => env('RESPONSE_COMPRESSION_ALGORITHM', 'gzip'),

/**
 * The minimum length of the response content to be compressed.
 */
'min_length' => env('RESPONSE_COMPRESSION_MIN_LENGTH', 1024),

'gzip' => [
    /**
     * The level of compression. Can be given as 0 for no compression up to 9
     * for maximum compression. If not given, the default compression level will
     * be the default compression level of the zlib library.
     *
     * @see https://www.php.net/manual/en/function.gzencode.php
     */
    'level' => env('RESPONSE_COMPRESSION_GZIP_LEVEL', 5),
],

'br' => [
    /**
     * The level of compression. Can be given as 0 for no compression up to 11
     * for maximum compression. If not given, the default compression level will
     * be the default compression level of the brotli library.
     *
     * @see https://www.php.net/manual/en/function.brotli-compress.php
     */
    'level' => env('RESPONSE_COMPRESSION_BROTLI_LEVEL', 5),
]
```

---

## Before vs After

| Before | After |
|---------|---------|
| <img src="https://github.com/user-attachments/assets/906ad545-d9a0-4aad-85ad-8dcbd9c1d5e3" width="100%"> | <img src="https://github.com/user-attachments/assets/a0191b26-b86b-483a-ba07-699792840db5" width="100%"> |

---

## Testing

```bash
composer test
```

---

## Contributing

Contributions are welcome! Submit a pull request or open an issue to discuss new features or improvements.

---

## License

The MIT License (MIT). Please see [License File](https://github.com/chr15k/laravel-response-compression/blob/main/LICENSE) for more information.
