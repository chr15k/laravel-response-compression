<?php

declare(strict_types=1);

namespace Chr15k\ResponseCompression\Encoders;

use Chr15k\ResponseCompression\Contracts\Encoder;
use Symfony\Component\HttpFoundation\Response;

final class BrotliEncoder implements Encoder
{
    public function handle(Response $response): Response
    {
        if (function_exists('brotli_compress')) {
            $compressed = brotli_compress((string) $response->getContent(), $this->level());

            if ($compressed) {
                $response->setContent($compressed);

                $response->headers->add([
                    'Content-Encoding' => 'br',
                    'Vary'             => 'Accept-Encoding',
                    'Content-Length'   => strlen($compressed),
                ]);
            }
        }

        return $response;
    }

    public function level(): int
    {
        $level = config('response-compression.brotli.level');

        return is_int($level) && $level >= 0 && $level <= 11 ? $level : 5;
    }
}
