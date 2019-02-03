<?php

namespace App\Services;

class MessageService {

    function setGeneralMsgInSession(string $message, bool $isError)
    {
        $_SESSION["generalMsg"] = $message."___TIMESTAMP___".time();
        $_SESSION["isErrorMessage"] = $isError;
    }
}
