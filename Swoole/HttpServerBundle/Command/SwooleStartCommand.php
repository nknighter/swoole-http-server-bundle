<?php

namespace Swoole\HttpServerBundle\Command;

use Swoole\HttpServerBundle\Http\Request;
use Swoole\HttpServerBundle\Http\Response;
use Swoole\HttpServerBundle\Websocket\Event as WebsocketEvent;
use Swoole\HttpServerBundle\Websocket\Events as WebsocketEvents;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SwooleStartCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('swoole:start')
            ->setDescription('Start Swoole HTTP server')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host for server', '127.0.0.1')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'Port for server', 2345)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');
        $router = $this->getContainer()->get('router');

        $server = new \swoole_websocket_server(
            $input->getOption('host'),
            $input->getOption('port'),
            SWOOLE_BASE
        );
        
        $server->on('request', function ($req, $res) use ($kernel, $server) {
            $symfonyRequest = Request::createSymfonyRequest($req);
            $symfonyResponse = $kernel->handle($symfonyRequest);

            Response::send($res, $symfonyResponse);
            $kernel->terminate($symfonyRequest, $symfonyResponse);
        });
        
        $dispatcher = new EventDispatcher();
        
        $server->on('handshake', function ($request, $response) {});
        
        $server->on('open', function ($server, $request) {
            $event = new WebSocketEvent($server, $request->fd);
            $dispatcher->dispatch(WebSocketEvents::onOpen, $event);
        });
        
        $server->on('message', function ($server, $frame) {
            $event = new WebSocketEvent($server, $frame->fd);
            $dispatcher->dispatch(WebSocketEvents::onMessage, $event);
        });
        
        $server->on('close', function ($server, $fd) {
            $event = new WebSocketEvent($server, $fd);
            $dispatcher->dispatch(WebSocketEvents::onClose, $event);
        });
        
        $server->start();
    }
}