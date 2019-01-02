<?php

namespace App\controllers;

use \App\services\UserService;
use \App\services\DoctorService;
use \App\services\PatientService;
use \App\services\InterventionService;
use \App\services\AppointmentService;
use \App\services\ResetPasswordService;
use \App\services\DatabaseConnection;

class UserController
{
    private $pdo;

    function __CONSTRUCT()
    {
        session_start();
        if(!isset($_SESSION["username"]))
        {
            header("Location: /login");
        }

        $databaseConnectionInstance = new DatabaseConnection();
        $this->pdo = $databaseConnectionInstance->CreateDatabaseConnection();
    }

    public function userPageAction(array $params, array $query) {
        if(isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"])
        {
            header("Location: /admin");
        }
        else
        {
            header("Location: /doctor");
        }
        // /user
    }

    public function adminPageAction(array $params, array $query) {
        if(isset($_SESSION["isAdmin"]) && !$_SESSION["isAdmin"])
        {
            header("Location: /");
        }

        $doctorInstance = new DoctorService($this->pdo);
        $doctorInstance->getDoctors();

        $interventionInstance = new InterventionService($this->pdo);
        $interventionInstance->getInterventions();

            include(__DIR__ . "\..\htmls\adminpage.phtml");
       // /admin
    }

   public function doctorPageAction(array $params, array $query) {
        if(isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"])
        {
            header("Location: /");
        }

        unset($_SESSION["selectedPatient"]);
        unset($_SESSION["addedPatient"]);
        unset($_SESSION["addedAppointment"]);

        $patientInstance = new PatientService($this->pdo);
        $patientInstance->getPatients();

        $appointmentsInstance = new AppointmentService($this->pdo);
        $appointmentsInstance->getAppointments();

        include(__DIR__ . "\..\htmls\doctorpage.phtml");
        // /doctor
    }

    public function userEditAction(array $params, array $query) {
         include(__DIR__ . "\..\htmls\usereditpage.phtml");
        // /user/edit
    }

    public function userSaveAction(array $params, array $query) {
        $editInstance = new UserService($this->pdo);
        $editInstance->editProfile($params["firstname"], $params["lastname"], $params["telephone"]);
        header("Location: /user/");
        // /user/save
    }

    public function userStatusAction(array $params, array $query) {
        if($params["idUser"] == null || $params["isActive"] == null)
        {
            header("Location: /notfound");
        }

        $editInstance = new UserService($this->pdo);
        $editInstance->changeStatus($params["idUser"], $params["isActive"]);
        header("Location: /user/");
        // /user/save
    }

    public function resetPasswordAction(array $params, array $query) {
        $resetPasswordInstance = new ResetPasswordService($this->pdo);
        $reseted = $resetPasswordInstance->resetPassword($params["password"],$params["confirm_password"]);

        if($reseted) {
            header("Location: /user/edit");
        } else {
            header("Location: /notfound");
        }
        // /user/resetpassword
    }
}
