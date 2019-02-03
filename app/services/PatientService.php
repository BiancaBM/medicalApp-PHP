<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Appointment;

use App\Services\MessageService;

class PatientService { 

    private $messageInstance;

    function __construct() {
        $this->messageInstance = new MessageService();
    }

    function getPatients()
    {   
        $patients = (new Patient)->getBy("idUser", $_SESSION["idUser"], true);
        return $patients ? $patients : [];  
    }

    function removePatient(string $patientId = null, string $patientName = null){
        if($patientId == null && $patientName == null){
            header('Location: /notfound');
            return false;
        }

        if($this->existAppointmentRelatedToPatient($patientId)) {
            $this->messageInstance->setGeneralMsgInSession('Imposible to remove patient '.$patientName.'! First remove appointments related to this patient!', true);
            header('Location: /user');
            return false;
        }

        (new Patient)->deleteBy("idPatient", $patientId);
        
        $this->messageInstance->setGeneralMsgInSession('Patient '.$patientName.' was successfully removed!', false);
        header('Location: /');
    }

    function existAppointmentRelatedToPatient(string $idPatient) {
        $results = (new Appointment)->getBy("idPatient", $idPatient, true);

        return count($results) > 0;
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
        
        $this->messageInstance->setGeneralMsgInSession("Patient ".$lastName." ".$firstName." updated!", false);
    }

    function addPatient(string $firstName = null, string $lastName = null, string $cnp = null, string $telephone = null, string $address = null)
    {   
        if($idPatient == null || $firstName == null || $lastName == null || $telephone == null || $cnp == null) {
            header('Location: /notfound');
        }

        if($this->checkCnpExists($cnp)){
            $this->messageInstance->setGeneralMsgInSession("A patient with same CNP already exist!", true);
            return FALSE;
        } 
            
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

        $this->messageInstance->setGeneralMsgInSession("Patient ".$lastName." ".$firstName." successfully added!", false);
  
        return TRUE;
    }

    function checkCnpExists(string $cnp)
    {
        $result = (new Patient)->getBy('cnp', $cnp);
        return $result;
    }

}
