<?php namespace FszTeam\ManageEvents\Classes;

use FszTeam\ManageEvents\Models\GeneratedDate;

/**
 * print out iCal views
 * Requires min php 5.3  
 *
 * @package fszteam/manageevents
 * @author ChadStrat
 */
class IcalManager
{
    public function __construct($type=null)
    {

    }
    
    public static function getAllEvents(){
        return GeneratedDate::getEventsList();
    }

}