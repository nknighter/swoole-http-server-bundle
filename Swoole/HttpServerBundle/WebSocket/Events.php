<?php

namespace Swoole\HttpServerBundle\Websocket;

final class Events
{
    const onHandshake = 'socket.handshake';
    const onOpen = 'socket.open';
    const onMessage = 'socket.message';
    const onClose = 'socket.close';
}