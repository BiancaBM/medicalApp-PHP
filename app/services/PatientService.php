<?php

namespace App\services;

use PDO;

class PatientService { 

    private $pdo;

    function __CONSTRUCT(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    function getPatients()
    {   
         $sql="SELECT * FROM patients WHERE idUser=(?)";
         $stmt = $this->pdo->prepare($sql);
         $status = $stmt->execute([$_SESSION["idUser"]]);

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        else
        {
            $patients= [];
            while($row = $stmt->fetch()) {
                array_push($patients, $row);
            }
            
            $_SESSION["patients"]=$patients;
        } 
    }

    function removePatient(string $patientId = null, string $patientName = null){
        if($patientId == null && $patientName == null){
            header('Location: /notfound');
            return false;
        }

        if($this->existAppointmentRelatedToPatient($patientId)) {
            $_SESSION["generalMsg"] = 'Imposible to remove patient '.$patientName.'! First remove appointments related to this patient!';
            header('Location: /user');
            return false;
        }
        
        $sql="DELETE FROM patients WHERE idPatient=(?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$patientId]);

        if($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
            $_SESSION["generalMsg"] = 'Error removing '.$patientName.'!';
        } else {
            $_SESSION["generalMsg"] = 'Patient '.$patientName.' was successfully removed!';
            header('Location: /');
        }
    }

    function existAppointmentRelatedToPatient(string $idPatient) {
        $sql="SELECT * FROM appointments WHERE idPatient=(?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$idPatient]);

        if($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
            return true;
        } else if(count($stmt->fetchAll()) > 0) {
            return true;
        }

        return false;
    }

    function setSelectedPatient(string $idPatient = null, string $firstName = null, string $lastName = null, string $cnp = null, string $telephone = null, string $address = null)
    {   
        if($idPatient == null) {
            header('Location: /notfound');
        }

        $selectedPatient = [];
        $selectedPatient["idPatient"]=$idPatient;
        $selectedPatient["firstName"]=$firstName;
        $selectedPatient["lastName"]=$lastName;
        $selectedPatient["cnp"]=$cnp;
        $selectedPatient["telephone"]=$telephone;
        $selectedPatient["address"]=$address;

        $_SESSION["selectedPatient"] = $selectedPatient;
    }

    function editPatient(string $idPatient = null, string $firstName = null, string $lastName = null, string $telephone = null, string $address = null)
    {   
        if($idPatient == null) {
            header('Location: /notfound');
        }

        $sql="UPDATE `patients` SET `firstName` = (?),`lastName` = (?), `telephone` = (?), `address` = (?) WHERE `idPatient` = (?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$firstName,$lastName,$telephone,$address, $idPatient]);
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        else
        {
            unset($_SESSION["selectedPatient"]);
            $_SESSION["generalMsg"] = "Patient ".$lastName.' '.$firstName.' updated!';
        } 
    }

    function addPatient(string $firstName = null, string $lastName = null, string $cnp = null, string $telephone = null, string $address = null)
    {   
        if($idPatient == null || $firstName == null || $lastName == null || $telephone == null || $cnp == null) {
            header('Location: /notfound');
        }

        if($this->checkCnpExists($cnp)){
            $_SESSION["generalMsg"] = "A patient with same CNP already exist!";
            return FALSE;
        } else {
            $sql = "INSERT INTO `patients` (firstName, lastName, telephone, cnp, address, idUser) VALUES(?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($sql);
            $status = $stmt->execute([$firstName,$lastName,$telephone,$cnp,$address, $_SESSION['idUser']]);
            if ($status === false) {
                trigger_error($stmt->error, E_USER_ERROR);
                return FALSE;
            }
            else
            {
                $_SESSION["generalMsg"] = "Patient ".$lastName.' '.$firstName.' successfully added!';
            } 
        }
        
        return TRUE;
    }

    function checkCnpExists(string $cnp)
    {
        $sql = "SELECT * FROM patients WHERE cnp = (?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cnp]);
        $result = $stmt->fetch();
        if ($result) {
            return true;
        }

        return false;
    }

}
