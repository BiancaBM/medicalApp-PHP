<?php

namespace App\controllers;

class NotFoundController
{
    public function notFoundPageAction(array $params, array $query) {
        include(__DIR__ . '\..\htmls\notfoundpage.phtml');
        // /login
    }
}
