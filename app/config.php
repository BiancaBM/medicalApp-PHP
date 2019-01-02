<?php

$config = [
    'env' => 'dev',
];

if ($config['env'] == 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
else {
    ini_set('display_errors', 0);
}
