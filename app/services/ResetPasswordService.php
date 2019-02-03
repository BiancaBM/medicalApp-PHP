<?php

namespace App\Services;

use App\Models\User;
use App\Services\MessageService;

class ResetPasswordService { 

    private $messageInstance;

    function __construct() {
        $this->messageInstance = new MessageService();
    }

    function resetPassword(string $password = null, string $confirm_password = null)
    {
        if($password === $confirm_password && $password != null)
        {
            $password=password_hash($password,PASSWORD_DEFAULT);
            $data = ['password' => $password];
            $where = ['idUser' => $_SESSION["idUser"]];
            (new User)->update($where, $data);
            
            $this->messageInstance->setGeneralMsgInSession("Password has been reset successfully!", false);
            return TRUE;
        }

        return FALSE;
    }
}
