<?php
declare(strict_types=1);

namespace BenyCode\Slim\RequestLoggerMiddleware\Transformer;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use function microtime;
use function round;

final class ResponseTransformer
{
    public static function transformResponsetData(ServerRequestInterface $request, ResponseInterface $response): array
    {
        $requesStartedAt = $request
            ->getAttribute('request_started_at')
            ;

        $body = (string)$response
            ->getBody()
            ;

        $responseContentType = null;

        if(isset($response->getHeader('Content-Type')[0])){
            $responseContentType = $response
                ->getHeader('Content-Type')[0]
                ;
        }

        $responseEndedAt = microtime(true);

        $responseTook = round(microtime(true) - $requesStartedAt,3)*1000;
     
        return [
            'response_status' => $response->getStatusCode(),
            'response_content_type' => $responseContentType,
            'response_content' => $body,
            'response_content_length' => mb_strlen($body),
            'response_took' => $responseTook,
        ];
    }
}
