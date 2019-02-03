<?php

namespace App\Services;

use App\Models\Intervention;
use App\Models\AppointmentIntervention;
use App\Services\MessageService;

class InterventionService { 

    private $messageInstance;

    function __construct() {
        $this->messageInstance = new MessageService();
    }

    function getInterventions() {
        $interventions = (new Intervention)->getAll();
        return $interventions ? $interventions : [];
    }

    function addIntervention(string $name = null, string $price = null)
    {   
        if($name == null || $price == null) {
            header('Location: /notfound');
        }

        $itemToInsert = ['name' => $name, 'price' => $price];
        $insertedId = (new Intervention)->insert($itemToInsert);

        $this->messageInstance->setGeneralMsgInSession('Intervention '.$name.' successfully added!', false);
        header('Location: /user');
    }

    function removeIntervention(string $idIntervention = null){
        if($idIntervention == null){
            header('Location: /notfound');
            return false;
        }

        if(!$this->canRemoveIntervation($idIntervention)){
            $this->messageInstance->setGeneralMsgInSession("This intervention was used by your doctors! You can't remove it!", true);
            header('Location: /user');
            return false;
        }

        (new Intervention)->deleteBy("idIntervention", $idIntervention);
        
        $this->messageInstance->setGeneralMsgInSession("Selected intervention was successfully removed!", false);
        header('Location: /');
    }

    function canRemoveIntervation(string $idIntervention){
        $results = (new AppointmentIntervention)->getBy('idIntervention', $idIntervention, true);
        return !$results;
    }
}
