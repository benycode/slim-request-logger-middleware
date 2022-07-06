<?php
declare(strict_types=1);

namespace BenyCode\Slim\RequestLoggerMiddleware\Mask;

final class HeaderDataMask
{

    public function __construct(
        private array $headers,
    ) {
    }

    public function withAuthorizationMask(): self
    {
        $headers = [];

        foreach($this->headers as $key => $header){
  
            if(strtolower($key) === 'authorization'){
                $headers[$key][0] = '*********';
            } else {
                $headers[$key] = $header;
            }            
        }

        $this->headers = $headers;

        return $this;
    }

    public function getHeaders(){
        return $this
            ->headers
            ;
    }
}
