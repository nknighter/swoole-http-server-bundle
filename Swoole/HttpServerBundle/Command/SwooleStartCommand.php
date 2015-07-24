<?php


namespace Swoole\HttpServerBundle\Command;

use Swoole\HttpServerBundle\Http\Request;
use Swoole\HttpServerBundle\Http\Response;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

        $http = new \swoole_http_server($input->getOption('host'), $input->getOption('port'));

        $http->on('request', function ($req, $res) use ($kernel, $http) {
            $symfonyRequest = Request::createSymfonyRequest($req);
            $symfonyResponse = $kernel->handle($symfonyRequest);

            Response::send($res, $symfonyResponse);
            $kernel->terminate($symfonyRequest, $symfonyResponse);
        });

        $http->start();
    }
}