<?php

namespace App\services;

use PDO;

class InterventionService { 

    private $pdo;

    function __CONSTRUCT(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    function getInterventions() {
        $sql= "SELECT * FROM interventions";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([]);

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        else
        {
            $interventions = [];
            while($row = $stmt->fetch()) {
                $intervention = [];
                $intervention["idIntervention"] = $row["idIntervention"];
                $intervention["name"] = $row["name"];
                $intervention["price"] = $row["price"];
                array_push($interventions, $intervention);
            }
            
            $_SESSION["interventions"] = $interventions;
        }
    }

    function addIntervention(string $name = null, string $price = null)
    {   
        if($name == null || $price == null) {
            header('Location: /notfound');
        }

        $sql = "INSERT INTO `interventions` (name, price) VALUES(?,?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$name,$price]);
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        else
        {
            $_SESSION["generalMsg"] = 'Intervention '.$name.' successfully added!';
            header('Location: /user');
        } 
    }

    function removeIntervention(string $idIntervention = null){
        if($idIntervention == null){
            header('Location: /notfound');
            return false;
        }

        if(!$this->canRemoveIntervation($idIntervention)){
            $_SESSION["generalMsg"] = "This intervention was used by your doctors! You can't remove it!";
            header('Location: /user');
            return false;
        }

        $sql="DELETE FROM interventions WHERE idIntervention=(?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$idIntervention]);

        if($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
            $_SESSION["generalMsg"] = "Error removing selected intervention!";
        } else {
            $_SESSION["generalMsg"] = "Selected intervention was successfully removed!";
            header('Location: /');
        }
    }

    function canRemoveIntervation(string $idIntervention){
        $sql="SELECT * FROM appinterv WHERE idIntervention=(?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$idIntervention]);
        
        if($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
            return FALSE;
        } else if(count($stmt->fetchAll()) > 0){
            return FALSE;
        }
        return TRUE;
    }
}
