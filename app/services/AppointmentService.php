<?php

namespace App\services;

use PDO;

class AppointmentService { 

    private $pdo;

    function __CONSTRUCT(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    function getAppointments()
    {   
         $sql= "SELECT * FROM appointments WHERE idUser=(?) ";
         $stmt = $this->pdo->prepare($sql);
         $status = $stmt->execute([$_SESSION["idUser"]]);

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        else
        {
            $appointments = [];
            while($row = $stmt->fetch()) {
                $appointment = [];
                $appointment["idAppointment"] = $row["idAppointment"];
                $appointment["idPatient"] = $row["idPatient"];
                $appointment["startDate"] = $row["startDate"];
                $appointment["endDate"] = $row["endDate"];
                $this->fillPatientName($appointment);
                $this->fillInterventionsAndTotalPrice($appointment);
                array_push($appointments, $appointment);
            }
            
            $_SESSION["appointments"]=$appointments;
        } 
    }

    function fillPatientName(&$appointment) {
        $sql= "SELECT * FROM patients WHERE idPatient=(?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$appointment["idPatient"]]);
        $result = $stmt->fetch();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        else
        {
            $appointment["patientFullName"] = $result["lastName"].' '.$result["firstName"];
            $appointment["patientCnp"] = $result["CNP"];
        }
    }

    function fillInterventionsAndTotalPrice(&$appointment) {
        $sql= "SELECT * FROM appinterv WHERE idAppointment=(?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$appointment["idAppointment"]]);
        
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        else
        {
            $interventions = [];
            $totalPrice = 0;
            while($row = $stmt->fetch()) {

                $sqlIntervention = "SELECT * FROM interventions WHERE idIntervention=(?)";
                $stmtIntervention = $this->pdo->prepare($sqlIntervention);
                $statusIntervention = $stmtIntervention->execute([$row["idIntervention"]]);
                $resultIntervention = $stmtIntervention->fetch();

                if($statusIntervention == false){
                    trigger_error($stmt->error, E_USER_ERROR);
                } else if($resultIntervention) {
                    $intervention = [];
                    $intervention["name"] = $resultIntervention["name"];
                    $intervention["price"] = $resultIntervention["price"];
                    $totalPrice+= $resultIntervention["price"];
    
                    array_push($interventions, $intervention);
                }
            }

            $appointment["interventions"] = $interventions;
            $appointment["totalPrice"] = $totalPrice;
        }
    }

    function removeAppointment(string $idAppointment = null){
        if($idAppointment == null){
            header('Location: /notfound');
            return false;
        }
        
        $this->removeLinkedInterventions($idAppointment);
        $sql="DELETE FROM appointments WHERE idAppointment=(?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$idAppointment]);

        if($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
            $_SESSION["generalMsg"] = "Error removing selected appointment!";
        } else {
            $_SESSION["generalMsg"] = "Selected appointment was successfully removed!";
            header('Location: /');
        }
    }

    function removeLinkedInterventions(string $idAppointment){
        $sql="DELETE FROM appinterv WHERE idAppointment=(?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$idAppointment]);

        if($status == false){
            trigger_error($stmt->error, E_USER_ERROR);
            $_SESSION["generalMsg"] = "Error removing linked interventions!";
        }
    }

    function addAppointment(string $selectedPatientId = null, string $startDate = null, string $endDate = null, array $selectedInterventions = null)
    {   
        if($selectedPatientId == null || $startDate == null || $endDate == null) {
            header('Location: /notfound');
        }

        if(!$this->isIntervalAvailable($startDate, $endDate)){
            $_SESSION["generalMsg"] = "Dates conflict! Please change it!";
            return FALSE;
        }     
        else {
            $sql = "INSERT INTO `appointments` (startDate, endDate, idPatient, idUser) VALUES(?,?,?,?)";
            $stmt = $this->pdo->prepare($sql);
            $status = $stmt->execute([$startDate,$endDate,$selectedPatientId,$_SESSION['idUser']]);
            $insertedAppointmentId = $this->pdo->lastInsertId();

            if ($status === false) {
                trigger_error($stmt->error, E_USER_ERROR);
                return FALSE;
            }
            else
            {
                if($insertedAppointmentId != null && $selectedInterventions != null && count($selectedInterventions) > 0){
                    $this->createIntervetionsAppointmentsLinks($insertedAppointmentId, $selectedInterventions);
                }

                $_SESSION["generalMsg"] = "Appointment successfully added!";
            } 
        }
        
        return TRUE;
    }

    function isIntervalAvailable(string $startDate, string $endDate) {
        $sql = "SELECT * FROM `appointments` WHERE (startDate >= (?) AND startDate <= (?)) OR (endDate >= (?) AND endDate <= (?))"; // check to not include other intervals inside of it
        $sql.= "OR ((?) >= startDate && (?) <= endDate) OR ((?) >= startDate && (?) <= endDate)"; // check that selected interval is not inside of another intervals
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([
            $startDate,$endDate,$startDate,$endDate,
            $startDate, $startDate, $endDate, $endDate
            ]);

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
            return FALSE;
        }
        else if(count($stmt->fetchAll()) > 0) {
            return FALSE;
        }

        return TRUE;
    }

    function createIntervetionsAppointmentsLinks(string $idAppointment, array $interventionIds){
        $sql = "INSERT INTO `appinterv` (idAppointment, idIntervention) VALUES (?,?)";
        $stmt = $this->pdo->prepare($sql);
        foreach ($interventionIds as &$idIntervention) {
            $status = $stmt->execute([$idAppointment, $idIntervention]);
    
            if ($status === false) {
                trigger_error($stmt->error, E_USER_ERROR);
            }
        }
    }
}
