<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Appointment;

class PatientService { 

    function __CONSTRUCT()
    {
    }

    function getPatients()
    {   
        $patients = (new Patient)->getBy("idUser", $_SESSION["idUser"], true);
        $_SESSION["patients"]=$patients ? $patients : [];  
    }

    function removePatient(string $patientId = null, string $patientName = null){
        if($patientId == null && $patientName == null){
            header('Location: /notfound');
            return false;
        }

        if($this->existAppointmentRelatedToPatient($patientId)) {
            $_SESSION["generalMsg"] = 'Imposible to remove patient '.$patientName.'! First remove appointments related to this patient!'.'___TIMESTAMP___'.time();
            $_SESSION["isErrorMessage"] = true;
            header('Location: /user');
            return false;
        }

        (new Patient)->deleteBy("idPatient", $patientId);

        $_SESSION["generalMsg"] = 'Patient '.$patientName.' was successfully removed!'.'___TIMESTAMP___'.time();
        $_SESSION["isErrorMessage"] = false;
        header('Location: /');
    }

    function existAppointmentRelatedToPatient(string $idPatient) {
        $results = (new Appointment)->getBy("idPatient", $idPatient, true);

        if(count($results) > 0) {
            return true;
        }

        return false;
    }

    function editPatient(string $idPatient = null, string $firstName = null, string $lastName = null, string $telephone = null, string $address = null)
    {   
        if($idPatient == null) {
            header('Location: /notfound');
        }

        $data = 
            [
                'firstName' => $firstName, 'lastName' => $lastName,
                'telephone' => $telephone, 'address' => $address
            ];

        $where = ['idPatient' => $idPatient];
        
        (new Patient)->update($where, $data);
        
        $_SESSION["generalMsg"] = "Patient ".$lastName.' '.$firstName.' updated!'.'___TIMESTAMP___'.time();
        $_SESSION["isErrorMessage"] = false;
    }

    function addPatient(string $firstName = null, string $lastName = null, string $cnp = null, string $telephone = null, string $address = null)
    {   
        if($idPatient == null || $firstName == null || $lastName == null || $telephone == null || $cnp == null) {
            header('Location: /notfound');
        }

        if($this->checkCnpExists($cnp)){
            $_SESSION["generalMsg"] = "A patient with same CNP already exist!"."___TIMESTAMP___".time();
            $_SESSION["isErrorMessage"] = true;
            return FALSE;
        } else {
            
            $itemToInsert = 
                [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'telephone' => $telephone,
                    'cnp' => $cnp,
                    'address' => $address,
                    'idUser' => $_SESSION['idUser']
                ];
           
                $insertedId = (new Patient)->insert($itemToInsert);

            $_SESSION["generalMsg"] = "Patient ".$lastName.' '.$firstName.' successfully added!'.'___TIMESTAMP___'.time();
            $_SESSION["isErrorMessage"] = false;
        }
        
        return TRUE;
    }

    function checkCnpExists(string $cnp)
    {
        $result = (new Patient)->getBy('cnp', $cnp);

        if ($result) {
            return true;
        }

        return false;
    }

}
