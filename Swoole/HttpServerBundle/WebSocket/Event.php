<?php

namespace Swoole\HttpServerBundle\Websocket;

use Symfony\Component\EventDispatcher\Event as DispatcherEvent;

class Event extends DispatcherEvent
{
    protected $fd;
    protected $server;

    public function __construct($fd, \swoole_websocket_server $server)
    {
        $this->fd = $fd;
        $this->server = $server;
    }
    
    public function getFd()
    {
        return $this->fd;
    }

    public function getServer()
    {
        return $this->server;
    }
}