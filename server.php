<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

if (preg_match('#^/build/assets/app-[A-Za-z0-9_-]+\.(css|js)$#D', $uri) && is_file($publicPath.$uri)) {
    $assetPath = $publicPath.$uri;

    header('Cache-Control: public, max-age=31536000, immutable');
    header(str_ends_with($uri, '.css') ? 'Content-Type: text/css; charset=UTF-8' : 'Content-Type: application/javascript');
    readfile($assetPath);

    return true;
}

if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

require_once $publicPath.'/index.php';
