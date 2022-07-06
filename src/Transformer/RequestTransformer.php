<?php
declare(strict_types=1);

namespace BenyCode\Slim\RequestLoggerMiddleware\Transformer;

use BenyCode\Slim\RequestLoggerMiddleware\Mask\HeaderDataMask;
use Psr\Http\Message\ServerRequestInterface;

final class RequestTransformer
{
    public static function transformRequestData(ServerRequestInterface $request): array
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

        $headers = $request
            ->getHeaders()
            ;

        $maskedHeaders = (new HeaderDataMask($headers))
            ->withAuthorizationMask()
            ->getHeaders()
            ;

        return [
            'request_content' => $body,
            'request_headers' => json_encode($maskedHeaders),
            'request_agent' => $requestAgent,
            'request_method' => $request->getMethod(),
            'request_content_type' => $requestContentType,
            'request_content_length' => mb_strlen($body),
            'request_query_string' => $request->getUri(),
            'client_ip' => $request->getAttribute('ip_address'),
        ];
    }
}
