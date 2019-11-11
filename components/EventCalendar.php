<?php namespace FszTeam\ManageEvents\Components;


use Cms\Classes\ComponentBase;
use Carbon\Carbon;
use Cms\Classes\Page;
use FszTeam\ManageEvents\Models\Event  as EventModel;
use FszTeam\ManageEvents\Models\Calendar  as CalendarModel;
use FszTeam\ManageEvents\Models\GeneratedDate  as GeneratedDateModel;
use FszTeam\ManageEvents\Classes\EventListHelper;
use Request;

class EventCalendar extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'fszteam.manageevents::lang.components.event_calendar.details.name',
            'description' => 'fszteam.manageevents::lang.components.event_calendar.details.description'
        ];
    }

    public function defineProperties()
    {
        $calendars = self::getCalendars();
        $calendars_select_options = array('all_calendars'=>trans('fszteam.manageevents::lang.components.event_calendar.properties.calendar.options.all_calendars'));
        foreach($calendars as $calendar){
            $calendars_select_options[$calendar->id] = $calendar->name;
        }
        return [
            'eventpage' => [
                'title'       => 'fszteam.manageevents::lang.components.event_calendar.properties.eventpage.title',
                'description' => 'fszteam.manageevents::lang.components.event_calendar.properties.eventpage.description',
                'type'        => 'dropdown'
            ],
            'style' => [
                'description' => 'fszteam.manageevents::lang.components.event_calendar.properties.style.description',
                'title'       => 'fszteam.manageevents::lang.components.event_calendar.properties.style.title',
                'default'     => 'responsive',
                'type'        => 'dropdown',
                'options'     => [
                    'responsive'=>'fszteam.manageevents::lang.components.event_calendar.properties.style.options.responsive',
                    'jquery_fullcalendar'=>'fszteam.manageevents::lang.components.event_calendar.properties.style.options.jquery',
                    'smallcal'=>'fszteam.manageevents::lang.components.event_calendar.properties.style.options.smallcal'
                ]
            ],
            'calendar' => [
                'description' => 'fszteam.manageevents::lang.components.event_calendar.properties.calendar.description',
                'title'       => 'fszteam.manageevents::lang.components.event_calendar.properties.calendar.title',
                'default'     => 'all_calendars',
                'type'        => 'dropdown',
                'options'     => $calendars_select_options
            ],
            'eurocal' => [
                'description' => 'fszteam.manageevents::lang.components.event_calendar.properties.euro.description',
                'title'       => 'fszteam.manageevents::lang.components.event_calendar.properties.euro.title',
                'type'        => 'checkbox'
            ],
            'ical' => [
                'description' => 'fszteam.manageevents::lang.components.event_calendar.properties.ical.description',
                'title'       => 'fszteam.manageevents::lang.components.event_calendar.properties.ical.title',
                'type'        => 'checkbox'
            ]
        ];
    }


    public function onRun()
    {
        $this->page['event_calendar_view'] = $this->property('style');
        $this->prepareEventList();

        if($this->property('style') == 'jquery_fullcalendar'){
            $this->addJs('/plugins/fszteam/manageevents/assets/js/moment.min.js');
            $this->addCss('/plugins/fszteam/manageevents/assets/css/fullcalendar.css');
            $this->addJs('/plugins/fszteam/manageevents/assets/js/fullcalendar.min.js');
            $this->addJs('/plugins/fszteam/manageevents/assets/js/locale-all.js');
            $this->addCss('/plugins/fszteam/manageevents/assets/css/jquery.qtip.min.css');
            $this->addJs('/plugins/fszteam/manageevents/assets/js/jquery.qtip.min.js');
        }else{
            $this->addCss('/plugins/fszteam/manageevents/assets/css/manageevents_calendar.css');
            $this->addCss('/plugins/fszteam/manageevents/assets/css/manageevents_tooltips.css');
            $this->addJs('/plugins/fszteam/manageevents/assets/js/manageevents_tooltips.js');
            $this->addCss('/plugins/fszteam/manageevents/assets/css/manageevents_modal.css');
            $this->addJs('/plugins/fszteam/manageevents/assets/js/manageevents_modal.js');
        }
    }

    public function getEventpageOptions()
    {
        $ParentOptions = array(''=>trans('fszteam.manageevents::lang.components.event_calendar.properties.eventpage.options.chose'));
        $pages = Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');

        $ParentOptions = array_merge($ParentOptions, $pages);

        //\Log::info($ParentOptions);
        return $ParentOptions;
    }

    public function getCalendarIDs(){

        $calendar_ids = null;

        /*
        * if we have a calendar property
        * and it's not 'All Calendars'
        * assemble calendar ID filters
        */
        $calendar = $this->property('calendar');

        if($this->param('calendar') && $this->param('calendar') != ':calendar'){
            $cal = CalendarModel::where('slug',$this->param('calendar'))->first();
            $calendar = $cal->id;
        }

        $calendar_ids = EventListHelper::getCalendarIDs($calendar);

        return $calendar_ids;
    }

    public function getCalendars(){
        return CalendarModel::get()->all();
    }

    public function getCalendarColors(){
        /*
        * get/set calendar_colors array
        */
        return EventListHelper::getCalendarColors();
    }


    /*
     * @isEvent
     * - for individual date check
     * "is there an event here on this day?"
     */
    public function isEvent($date){

        $calendar_ids = $this->getCalendarIDs();

        if($calendar_ids){
            $isEvent = GeneratedDateModel::dayHasEvents($date,$calendar_ids);
        }else{
            $isEvent = GeneratedDateModel::dayHasEvents($date);
        }
        if(is_object($isEvent)){
            return true;
        }else{
            return false;
        }
    }


    public function listEvents($month=null,$start=null,$end=null)
    {
        if ($this->events !== null)
            return $this->events;

        $calendar_ids = $this->getCalendarIDs();

        /*
        * get/set calendar_colors array
        */
        $this->page['calendar_colors'] = self::getCalendarColors();

        $events = new GeneratedDateModel();
        /*
        * return list of events
        */
        if($start){
            return $this->events = $events->getFromToCalendarEvents($start,$end,$calendar_ids);
        }else{
            return $this->events = $events->getCalendarEvents($month,$calendar_ids);
        }
    }

    /*
     * @onGetMonthEvents
     * - for jQuery Fullcalendar
     * jQuery Fullcalendar requires subtle considerations
     * for multiday events as well as formatting.
     */
    public function onGetMonthEvents(){

        $start = date('Y-m-d',strtotime($_REQUEST['start']));
        $end = date('Y-m-d',strtotime($_REQUEST['end']));

        $calendar_colors = self::getCalendarColors();

        $events = $this->listEvents(null,$start,$end);

        $event_item = array();

        $skip_events = array();

        foreach($events as $event){
            $color = '';
            $temp_event_array = array();

            $path = '/'.$this->property('eventpage').'/'.strtolower(str_replace(' ','-',$event->title)).'/'.$event->id.'/';

            /* Get colors array */
            if($event->calendar_id){
                $color = '#'.$calendar_colors[$event->calendar_id];
            }
            /*
             * if the event is daily and also an allday
             * then we loop and grab the last date of the same
             * event_id
             */
            if($event->recur == 'daily' && $event->allday > 0)
            {
                /* get end date */
                foreach($events as $levent)
                {
                    if($levent->event_id == $event->event_id)
                    {
                        $temp_event_array[] = $levent->event_date;
                        if($levent->id != $event->id)
                        {
                            /*
                             * add all days of this daily allday event
                             * to skip array
                             */
                            $skip_events[] = $levent->id;
                        }
                    }
                }
            }

            $temp_event_count = count( $temp_event_array );

            /*
             * if the temp loop count is > 0
             * then we know we have an allday daily recurring
             * else proceed as normal
             */
            if($temp_event_count > 0){
                $end_date = $temp_event_array[($temp_event_count - 1)];
            }else{
                $end_date = $event->event_date;
            }

            /*
             *  if this event is not skipped, add it
             */
            if(!in_array($event->id,$skip_events)){
                $event_item[] = array(
                    'id' => $event->id,
                    'title'=> $event->title,
                    'allDay' => $event->allday,
                    'start'=> $event->event_date.'T'.$event->sttime,
                    'end' => $end_date.'T'.$event->entime,
                    'color' => $color,
                    'url' => $path,
                    'description' => $event->excerpt
                );
            }
        }

        //\Log::info(json_encode($event_item));
        return json_encode($event_item);

    }

    protected function prepareEventList($date=null)
    {
        if(!$date){
            $date = date('Y-m-d');
            $month = date('m');
            $year = date('Y');
        }else{
            $date = date('Y-m-d',strtotime($date));
            $month = date('m',strtotime($date));
            $year = date('Y',strtotime($date));
        }

        $cDate = new Carbon($date);
        $this->page['cal_date'] = $date;
        $this->page['cal_title'] = $cDate->format('F Y');
        $this->page['year'] = $year ;
        $this->page['month'] = $month ;
        $this->page['days_in_month'] = date('t', strtotime($year . '-' . $month . '-01'));
        $this->page['first_day'] = $cDate->day(1)->format('Y-m-d') ;
        $this->page['day_of_week'] = $cDate->day(1)->format('D')  ;
        $this->page['euro_cal'] = $this->property('eurocal');
        //\Log::info(json_encode( $this->page['day_of_week'] ));
        switch($this->page['day_of_week']){
                case 'Mon' : if($this->page['euro_cal'] >= 1){$blank = 0;}else{$blank = 1;} break;
                case 'Tue' : if($this->page['euro_cal'] >= 1){$blank = 1;}else{$blank = 2;} break;
                case 'Wed' : if($this->page['euro_cal'] >= 1){$blank = 2;}else{$blank = 3;} break;
                case 'Thu' : if($this->page['euro_cal'] >= 1){$blank = 3;}else{$blank = 4;} break;
                case 'Fri' : if($this->page['euro_cal'] >= 1){$blank = 4;}else{$blank = 5;} break;
                case 'Sat' : if($this->page['euro_cal'] >= 1){$blank = 5;}else{$blank = 6;} break;
                case 'Sun' : if($this->page['euro_cal'] >= 1){$blank = 6;}else{$blank = 0;} break;
        }
        $this->page['blank'] = $blank;

        $this->page['events'] = $this->listEvents($month);
        $this->page['eventpage'] =  $this->property('eventpage');
    }


    public function onNextMonth(){
        $date = new Carbon($_POST['cal_date']);
        $date->addMonth();
        $this->prepareEventList($date->toDateString());
    }

    public function onPrevMonth(){
        $date = new Carbon($_POST['cal_date']);
        $date->subMonth();
        $this->prepareEventList($date->toDateString());
    }

    public function onSetMonth(){
        $date = new Carbon($_POST['cal_date']);
        $this->prepareEventList($date->toDateString());
    }

    public function onSetYear(){
        $date = new Carbon($_POST['cal_date']);
        $this->prepareEventList($date->toDateString());
    }

}
