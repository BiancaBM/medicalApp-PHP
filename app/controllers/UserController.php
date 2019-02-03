<?php

namespace App\controllers;

use Framework\Controller;
use App\Services\UserService;
use App\Services\DoctorService;
use App\Services\PatientService;
use App\Services\InterventionService;
use App\Services\AppointmentService;
use App\Services\ResetPasswordService;

class UserController extends Controller
{
    private $userInstance;
    private $interventionInstance;
    private $doctorInstance;
    private $patientInstance;
    private $appointmentsInstance;
    private $resetPasswordInstance;

    function __construct()
    {
        parent::__construct();
        $this->userInstance = new UserService();
        $this->interventionInstance = new InterventionService();
        $this->doctorInstance = new DoctorService();
        $this->patientInstance = new PatientService();
        $this->appointmentsInstance = new AppointmentService();
        $this->resetPasswordInstance = new ResetPasswordService();

        if(!isset($_SESSION["username"])) header("Location: /login");
    }

    public function userPageAction() {
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

    public function adminPageAction() {
        if(isset($_SESSION["isAdmin"]) && !$_SESSION["isAdmin"])
        {
            header("Location: /");
        }
        
        $doctors = $this->doctorInstance->getDoctors();
        $interventions = $this->interventionInstance->getInterventions();

        return $this->view('adminpage.html', ["doctors" => $doctors, "interventions" => $interventions]);
       // /admin
    }

   public function doctorPageAction() {
        if(isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"])
        {
            header("Location: /");
        }

        unset($_SESSION["addedPatient"]);
        unset($_SESSION["addedAppointment"]);

        $patients = $this->patientInstance->getPatients();
        $appointments = $this->appointmentsInstance->getAppointments();

        return $this->view('doctorpage.html', ["patients" => $patients, "appointments" => $appointments]);
        // /doctor
    }

    public function userEditAction() {
        return $this->view('usereditpage.html');
        // /user/edit
    }

    public function userSaveAction(array $params) {
        $this->userInstance->editProfile($params["firstname"], $params["lastname"], $params["telephone"]);
        header("Location: /user/");
        // /user/save
    }

    public function userStatusAction(array $params) {
        if($params["idUser"] == null || $params["isActive"] == null)
        {
            header("Location: /notfound");
        }

        $this->userInstance->changeStatus($params["idUser"], $params["isActive"]);
        header("Location: /user/");
        // /user/save
    }

    public function resetPasswordAction(array $params) {
        $reseted = $this->resetPasswordInstance->resetPassword($params["password"],$params["confirm_password"]);

        if($reseted) {
            header("Location: /user/edit");
        } else {
            header("Location: /notfound");
        }
        // /user/resetpassword
    }
}
