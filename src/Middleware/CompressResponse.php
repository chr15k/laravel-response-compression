<?php

declare(strict_types=1);

namespace Chr15k\ResponseCompression\Middleware;

use Chr15k\ResponseCompression\Encoders\BrotliEncoder;
use Chr15k\ResponseCompression\Encoders\GzipEncoder;
use Closure;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CompressResponse
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldCompress($request, $response)) {
            return $response;
        }

        return match (config('response-compression.algorithm')) {
            'gzip'  => app(GzipEncoder::class)->handle($response),
            'br'    => app(BrotliEncoder::class)->handle($response),
            default => $response,
        };
    }

    /**
     * Check if the response should be compressed.
     */
    private function shouldCompress(Request $request, Response $response): bool
    {
        return $this->enabled()
            && $this->validateRequest($request)
            && $this->validateResponse($response);
    }

    /**
     * Validate the response content.
     */
    private function validateResponse(Response $response): bool
    {
        $content = $response->getContent();

        return $response->isSuccessful()
            && is_string($content)
            && strlen($content) > config('response-compression.min_length')
            && $this->validateResponseType($response);
    }

    /**
     * Validate the response type.
     */
    private function validateResponseType(Response $response): bool
    {
        return ! $response instanceof BinaryFileResponse && ! $response instanceof StreamedResponse;
    }

    /**
     * Validate the request.
     */
    private function validateRequest(Request $request): bool
    {
        return in_array(
            config('response-compression.algorithm'),
            $request->getEncodings()
        );
    }

    /**
     * Check if compression is enabled.
     */
    private function enabled(): bool
    {
        return filter_var(
            config('response-compression.enabled'),
            FILTER_VALIDATE_BOOLEAN
        );
    }
}
