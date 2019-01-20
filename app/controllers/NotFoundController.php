<?php

namespace App\controllers;
use Framework\Controller;

class NotFoundController extends Controller
{
    public function notFoundPageAction() {
        return $this->view('notfoundpage.html');
    }
}
