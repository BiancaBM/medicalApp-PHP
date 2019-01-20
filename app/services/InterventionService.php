<?php

namespace App\Services;

use App\Models\Intervention;
use App\Models\AppointmentIntervention;

class InterventionService { 

    function getInterventions() {
        $interventions = (new Intervention)->getAll();
        $_SESSION["interventions"] = $interventions ? $interventions : [];
    }

    function addIntervention(string $name = null, string $price = null)
    {   
        if($name == null || $price == null) {
            header('Location: /notfound');
        }

        $itemToInsert = ['name' => $name, 'price' => $price];
        $insertedId = (new Intervention)->insert($itemToInsert);

        $_SESSION["generalMsg"] = 'Intervention '.$name.' successfully added!'.'___TIMESTAMP___'.time();
        $_SESSION["isErrorMessage"] = false;
        header('Location: /user');
    }

    function removeIntervention(string $idIntervention = null){
        if($idIntervention == null){
            header('Location: /notfound');
            return false;
        }

        if(!$this->canRemoveIntervation($idIntervention)){
            $_SESSION["generalMsg"] = "This intervention was used by your doctors! You can't remove it!"."___TIMESTAMP___".time();
            $_SESSION["isErrorMessage"] = true;
            header('Location: /user');
            return false;
        }

        (new Intervention)->deleteBy("idIntervention", $idIntervention);

        $_SESSION["generalMsg"] = "Selected intervention was successfully removed!"."___TIMESTAMP___".time();
        $_SESSION["isErrorMessage"] = false;
        header('Location: /');
    }

    function canRemoveIntervation(string $idIntervention){
        $results = (new AppointmentIntervention)->getBy('idIntervention', $idIntervention, true);
        return !$results;
    }
}
