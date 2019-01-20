<?php

require ('./vendor/autoload.php');
require_once ('./app/routes.php');


use \Framework\Router;

$router = new Router();

$router->route($static_routes, $_SERVER['REQUEST_URI']);
