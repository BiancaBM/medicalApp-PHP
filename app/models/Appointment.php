<?php

namespace App\Models;

use Framework\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

    function isIntervalAvailable(string $startDate, string $endDate) {
        $sql = "SELECT * FROM `appointments` WHERE (startDate >= (?) AND startDate <= (?)) OR (endDate >= (?) AND endDate <= (?))"; // check to not include other intervals inside of it
        $sql.= "OR ((?) >= startDate && (?) <= endDate) OR ((?) >= startDate && (?) <= endDate)"; // check that selected interval is not inside of another intervals
        $params = [
            $startDate,$endDate,$startDate,$endDate,
            $startDate, $startDate, $endDate, $endDate
        ];
        
        $result = $this->runScript($sql, $params);

        if(count($result) > 0) {
            return FALSE;
        }

        return TRUE;
    }
}