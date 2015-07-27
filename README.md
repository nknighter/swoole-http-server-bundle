Swoole Http Server Bundle
=======

Symfony bundle для https://github.com/swoole/swoole-src

Добавить в AppKernel:
    
    $bundles = array(
        ...
        new Swoole\HttpServerBundle\SwooleHttpServerBundle(),
        ...
    );
    
Запустить:
    
    php app/console swoole:start

    