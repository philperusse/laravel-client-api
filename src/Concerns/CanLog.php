<?php

namespace philperusse\Api\Concerns;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\NullLogger;

trait CanLog
{
    protected $logger;
    protected $messageFormatter;

    /** @var $handlerStack HandlerStack */
    protected $handlerStack;

    public function setLogger($logger = null, $messageFormatter = null)
    {
        $this->logger = $logger ?? new NullLogger;
        $this->messageFormatter = $messageFormatter;
    }

    public function hasLogger()
    {
        return !! $this->logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function createLoggingHandlerStack()
    {
        if($this->handlerStack)
            return $this->handlerStack;

        $this->handlerStack = HandlerStack::create();

        collect($this->messageFormatter)->each(function ($messageFormat) {
            $this->handlerStack->unshift(
                $this->createLoggingMiddleware($messageFormat)
            );
        });

        return $this->handlerStack;
    }

    public function createLoggingMiddleware(string $messageFormat)
    {
        return Middleware::log(
            $this->getLogger(),
            new MessageFormatter($messageFormat)
        );
    }
}