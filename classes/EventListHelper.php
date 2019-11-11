<?php

namespace FszTeam\ManageEvents\Classes;

use FszTeam\ManageEvents\Models\Event  as EventModel;
use FszTeam\ManageEvents\Models\Calendar  as CalendarModel;
use FszTeam\ManageEvents\Models\GeneratedDate  as GeneratedDateModel;
use Carbon\Carbon;

/**
 * This class parses multidate possibilities
 * Requires min php 5.3.
 *
 * @author ChadStrat
 */
class EventListHelper
{
    public function __construct()
    {

    }

    static function getCalendarIDs($calendar)
    {
        $calendar_ids = null;

        if($calendar && $calendar != 'all_calendars'){
            $calendars = explode(',',$calendar);
            $calendar_ids =  '(';
            $ci = 0;
            foreach($calendars as $id){
                if($ci>0){ $calendar_ids .= ' OR '; }
                $calendar_ids .=  "calendar_id = '$id'";
                $ci++;
            }
            $calendar_ids .=  ')';
        }

        return $calendar_ids;
    }

    static function getCalendars(){
        return CalendarModel::get()->all();
    }

    static function getCalendarColors(){
        /*
        * get/set calendar_colors array
        */
        $calendars = self::getCalendars();
        $calendars_array = array();
        foreach($calendars as $cal){
            $calendars_array[$cal->id] = $cal->color;
        }
        return $calendars_array;
    }
}
