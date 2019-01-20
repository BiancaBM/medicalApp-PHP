<?php

namespace App;

class Config
{
    const ENV = "dev";
    const DB = [
        "driver"  => "mysql",
        'host'    => 'localhost',
        'dbname'  => 'medical',
        'charset' => 'utf8mb4',
        'user'    => 'root',
        'pass'    => ''
    ];
}