<?php
include_once 'Request.php';
include_once 'Router.php';
$router = new Router(new Request);

$router->get('/', function() {
  return 'Hello World!';
});

$router->get('/ping', function($request) {
  return 'pong';
});

$router->post('/data', function($request) {
  return 'Hello Post Data';
});