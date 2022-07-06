# Slim Framework Request logger

A **Request** logger library for the Slim 4 Framework.

## Features

- Log request: content, headers, agent, method, content type, content length, query, ip;
- Log response: status, content, content type, content length, took time.

## Table of contents

- [Install](#install)
- [Usage](#usage)

## Install

Via Composer

``` bash
$ composer require benycode/slim-request-logger-middleware
```

Requires Slim 4.

## Usage

Use [DI](https://www.slimframework.com/docs/v4/concepts/di.html) to inject the library Middleware classes:

```php
use BenyCode\Slim\RequestLoggerMiddleware\RequestLogMiddleware;
use BenyCode\Slim\RequestLoggerMiddleware\ResponseLogMiddleware;
use BenyCode\Slim\RequestLoggerMiddleware\RequestLog\FileSystemLog;

return [
    ......
    LoggerFactory::class => function (ContainerInterface $container) {
        return new ... ; // use PSR-3 logger
    },
    RequestLogMiddleware::class => function (ContainerInterface $container) {

        $logger = $container->get(LoggerFactory::class)
            ->addFileHandler('requests.log')
            ->createLogger();

        $requestLog = new FileSystemLog($logger);

        return new RequestLogMiddleware($requestLog);
    },
    ResponseLogMiddleware::class => function (ContainerInterface $container) {

        $logger = $container->get(LoggerFactory::class)
            ->addFileHandler('requests.log')
            ->createLogger();

        $requestLog = new FileSystemLog($logger);

        return new ResponseLogMiddleware($requestLog);
    },
];
```

add a **Middlewares** to route globaly:

```php
use BenyCode\Slim\RequestLoggerMiddleware\RequestLogMiddleware;
use BenyCode\Slim\RequestLoggerMiddleware\ResponseLogMiddleware;

$app
  ->add(ResponseLogMiddleware::class)
  ->add(RequestLogMiddleware::class)
  ->add(RKA\Middleware\IpAddress::class)
  ;
```

create your own log output class:

```php
use BenyCode\Slim\RequestLoggerMiddleware\RequestLog;

final class AnyLogClass implements RequestLog
{
    public function __construct(
        ...
    ) {
    }

    public function logRequest(string $requestId, ServerRequestInterface $request): void
    {
        $requestData = RequestTransformer::transformRequestData($request); // you can use the own request data transformer
        
        ... // do something with the request log data
    }

    public function logResponse(ServerRequestInterface $request, ResponseInterface $response): void
    {
        $responseData = ResponseTransformer::transformResponsetData($request, $response); // you can use the own response data transformer

        ... // do something with the response log data
    }
}
```

inject your new log class to the logger:

```php
use BenyCode\Slim\RequestLoggerMiddleware\RequestLogMiddleware;
use BenyCode\Slim\RequestLoggerMiddleware\ResponseLogMiddleware;
use BenyCode\Slim\RequestLoggerMiddleware\RequestLog\FileSystemLog;
use ....\AnyLogClass;
return [
    ......
    RequestLogMiddleware::class => function (ContainerInterface $container) {

        $requestLog = new AnyLogClass(....);

        return new RequestLogMiddleware($requestLog);
    },
    ResponseLogMiddleware::class => function (ContainerInterface $container) {

        $requestLog = new AnyLogClass(....);

        return new ResponseLogMiddleware($requestLog);
    },
];
```
