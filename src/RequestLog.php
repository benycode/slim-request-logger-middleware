<?php
declare(strict_types=1);

namespace BenyCode\Slim\RequestLoggerMiddleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

interface RequestLog
{
    public function logRequest(string $requestId, ServerRequestInterface $request): void;

    public function logAuthenticatedRequest(ServerRequestInterface $request): void;

    public function logResponse(ServerRequestInterface $request, ResponseInterface $response): void;
}
