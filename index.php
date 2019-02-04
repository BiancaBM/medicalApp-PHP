<?php

require ('./vendor/autoload.php');
require_once ('./app/routes.php');

use \Framework\Router;


use Tracy\Debugger;
ini_set("display_errors", 0);
Debugger::enable(Debugger::PRODUCTION);
if(\App\Config::ENV === 'dev') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    Debugger::enable(Debugger::DEVELOPMENT);
}
//var_dump helper functions
//bd-> dump in the debugging bar
function bd($data) {
    bdump($data);
}
//dd-> dump on the page and die
function dd($data) {
    dump($data);
    die();
}


$router = new Router();

$router->route($static_routes, $_SERVER['REQUEST_URI']);
