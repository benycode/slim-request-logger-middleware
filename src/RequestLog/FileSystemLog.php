<?php
declare(strict_types=1);

namespace BenyCode\Slim\RequestLoggerMiddleware\RequestLog;

use BenyCode\Slim\RequestLoggerMiddleware\RequestLog;
use BenyCode\Slim\RequestLoggerMiddleware\Transformer\RequestTransformer;
use BenyCode\Slim\RequestLoggerMiddleware\Transformer\ResponseTransformer;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class FileSystemLog implements RequestLog
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function logRequest(string $requestId, ServerRequestInterface $request): void
    {
        $requestData = RequestTransformer::transformRequestData($request);

        $this
            ->logger
            ->info(sprintf('Request: %s', $requestId), $requestData)
            ;
    }

    public function logResponse(ServerRequestInterface $request, ResponseInterface $response): void
    {
        $responseData = ResponseTransformer::transformResponsetData($request, $response);

        $requestId = $request
            ->getAttribute('request_id')
            ;

        $this
            ->logger
            ->info(sprintf('Response: %s', $requestId), $responseData)
            ;

    }
}
