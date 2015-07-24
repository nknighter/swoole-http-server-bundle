<?php

namespace Swoole\HttpServerBundle\Http;

class Response
{
    public static function send($swResponse, $symfonyResponse)
    {
        foreach ($symfonyResponse->headers->getCookies() as $cookie) {
            $swResponse->header('Set-Cookie', $cookie);
        }

        foreach ($symfonyResponse->headers as $name => $values) {
            $name = implode('-', array_map('ucfirst', explode('-', $name)));
            foreach ($values as $value) {
                $swResponse->header($name, $value);
            }
        }

        $swResponse->end($symfonyResponse->getContent());
    }
}