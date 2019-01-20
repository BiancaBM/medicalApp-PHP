<?php

namespace App\controllers;

use Framework\Controller;
use App\Services\InterventionService;

class InterventionController extends Controller
{
    private $interventionInstance;
    function __construct()
    {   
        parent::__construct();
        if(!isset($_SESSION["username"])) header("Location: /");
        $this->interventionInstance = new InterventionService();
    }

    public function interventionAddPageAction() {
        if((isset($_SESSION['isAdmin']) && !$_SESSION['isAdmin'])){
            header('Location: /');
        }

        return $this->view('interventionaddpage.html');
    }

    public function interventionAddSaveAction(array $params) {
        if(!isset($_SESSION["idUser"]) || (isset($_SESSION['isAdmin']) && !$_SESSION['isAdmin'])) {
            header('Location: /notfound');
        }

        $this->interventionInstance->addIntervention($params["name"], $params["price"]);
        // /intervention/add/save
    }

    public function interventionRemoveAction(array $params) {
        $this->interventionInstance->removeIntervention($params["idIntervention"]);
        // /intervention/remove
    }
}
