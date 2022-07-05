<?php
declare(strict_types=1);

namespace BenyCode\Slim\RequestLoggerMiddleware;

use BenyCode\Slim\RequestLoggerMiddleware\RequestLog;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ResponseLogMiddleware
{
    public function __construct(
        private RequestLog $requestLog,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler
            ->handle($request)
            ;

        $this
            ->requestLog
            ->logResponse($request, $response)
            ;

        return $response;
    }
}
