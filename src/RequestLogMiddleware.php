<?php
declare(strict_types=1);

namespace BenyCode\Slim\RequestLoggerMiddleware;

use BenyCode\Slim\RequestLoggerMiddleware\RequestLog;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function microtime;

final class RequestLogMiddleware
{
    public function __construct(
        private RequestLog $requestLog,
    ) {
    }

    private function generateUuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0C2f ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0x2Aff ), mt_rand( 0, 0xffD3 ), mt_rand( 0, 0xff4B )
        );
    
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestStartedAt = microtime(true);

        $requestId = $this
            ->generateUuid()
            ;

        $requestLogId = $this
            ->requestLog
            ->logRequest($requestId, $request)
            ;

        $request = $request
            ->withAttribute('request_id', $requestId)
            ->withAttribute('request_started_at', $requestStartedAt)
            ;

        $response = $handler
            ->handle($request)
            ;
   
        return $response
            ->withHeader('Request-id', $requestId)
            ;
    }
}
