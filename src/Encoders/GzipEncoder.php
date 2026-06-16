<?php

declare(strict_types=1);

namespace Chr15k\ResponseCompression\Encoders;

use Chr15k\ResponseCompression\Contracts\Encoder;
use Symfony\Component\HttpFoundation\Response;

final class GzipEncoder implements Encoder
{
    public function handle(Response $response): Response
    {
        $compressed = gzencode((string) $response->getContent(), $this->level());

        if ($compressed) {
            $response->setContent($compressed);

            $response->headers->add([
                'Content-Encoding' => 'gzip',
                'Vary'             => 'Accept-Encoding',
                'Content-Length'   => strlen($compressed),
            ]);
        }

        return $response;
    }

    public function level(): int
    {
        $level = config('response-compression.gzip.level');

        return is_int($level) && $level >= -1 && $level <= 9 ? $level : 5;
    }
}
