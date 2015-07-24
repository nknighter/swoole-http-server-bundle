<?php

namespace Swoole\HttpServerBundle\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request
{
    public static function createSymfonyRequest($swRequest)
    {
        $_SERVER = isset($swRequest->server) ? array_change_key_case($swRequest->server, CASE_UPPER) : [];
        if (isset($swRequest->header)) {
            $headers = [];
            foreach ($swRequest->header as $k => $v) {
                $k = str_replace('-', '_', $k);
                $headers['http_' . $k] = $v;
            }
            $_SERVER += array_change_key_case($headers, CASE_UPPER);
        }

        $_GET = isset($swRequest->get) ? $swRequest->get : [];
        $_POST = isset($swRequest->post) ? $swRequest->post : [];
        $_COOKIE = isset($swRequest->cookie) ? $swRequest->cookie : [];

        $symfonyRequest = SymfonyRequest::createFromGlobals();
        if (0 === strpos($symfonyRequest->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($swRequest->rawContent(), true);
            $symfonyRequest->request->replace(is_array($data) ? $data : array());
        }

        return $symfonyRequest;
    }
}