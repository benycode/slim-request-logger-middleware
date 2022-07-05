<?php
declare(strict_types=1);

namespace BenyCode\Slim\RequestLoggerMiddleware\RequestLog;

use BenyCode\Slim\RequestLoggerMiddleware\RequestLog;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use function microtime;
use function round;

final class FileSystemLog implements RequestLog
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function logRequest(string $requestId, ServerRequestInterface $request): void
    {
        $requestData = $this
            ->transformRequestData($request)
            ;

        $this
            ->logger
            ->info(sprintf('Request: %s', $requestId), $requestData)
            ;
    }

    public function logAuthenticatedRequest(ServerRequestInterface $request): void
    {
        $requestData = $this
            ->transformAuthenticatedRequestData($request)
            ;

        $this
            ->queryFactory
            ->newUpdate('request_logs', $requestData)
            ->where(
                [
                    'id' => $request->getAttribute('request_log_id'),
                ]
            )
            ->execute()
            ;
    }

    public function logResponse(ServerRequestInterface $request, ResponseInterface $response): void
    {
        $responseData = $this
            ->transformResponsetData($request, $response)
            ;

        $requestId = $request
            ->getAttribute('request_id')
            ;

        $this
            ->logger
            ->info(sprintf('Response: %s', $requestId), $responseData)
            ;

    }

    private function transformResponsetData(ServerRequestInterface $request, ResponseInterface $response): array
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

        $responseEndedAt = (new DateTimeImmutable())
            ->format('Y-m-d H:i:s.u')
            ;

        $responseTook = round(microtime(true) - $requesStartedAt,3)*1000; ;
     
        return [
            'response_status' => $response->getStatusCode(),
            'response_content_type' => $responseContentType,
            'response_content' => $body,
            'response_content_length' => mb_strlen($body),
            'response_time' => $responseEndedAt,
            'response_took' => $responseTook,
        ];
    }

    private function transformAuthenticatedRequestData(ServerRequestInterface $request): array
    {
        $username = null;

        $token = $request
            ->getAttribute('token')
            ;

        if($token){
            if (isset($token['scope']) && isset($token['scope']->username)) {
                $username = $token['scope']->username;
            } else {
                throw new InvalidArgumentException('Request was authorized, but token has not username value.');
            }
        }

        return [
            'username' => $username,
        ];
    }

    private function transformRequestData(ServerRequestInterface $request): array
    {
        $body = $request
            ->getBody()
            ->getContents()
            ;

        $requestAgent = null;

        if(isset($request->getHeader('User-Agent')[0])){
            $requestAgent = $request
                ->getHeader('User-Agent')[0]
                ;
        }

        $requestContentType = null;

        if(isset($request->getHeader('Content-Type')[0])){
            $requestContentType = $request
                ->getHeader('Content-Type')[0]
                ;
        }

        return [
            'request_content' => $body,
            'request_headers' => json_encode($request->getHeaders()),
            'request_agent' => $requestAgent,
            'request_method' => $request->getMethod(),
            'request_content_type' => $requestContentType,
            'request_content_length' => mb_strlen($body),
            'request_query_string' => $request->getUri(),
            'client_ip' => $request->getAttribute('ip_address'),
        ];
    }
}
