<?php namespace FszTeam\ManageEvents\Components;


use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use FszTeam\ManageEvents\Models\Event  as EventModel;
use FszTeam\ManageEvents\Models\Calendar  as CalendarModel;
use FszTeam\ManageEvents\Models\GeneratedDate  as GeneratedDateModel;
use FszTeam\ManageEvents\Models\Settings as ManageEventsSettingsModel;
use Carbon\Carbon;
use Input;

class EventList extends ComponentBase
{
    public $monthDate = null;

    public function componentDetails()
    {
        return [
            'name'        => 'fszteam.manageevents::lang.components.event_list.details.name',
            'description' => 'fszteam.manageevents::lang.components.event_list.details.description'
        ];
    }

    public function defineProperties()
    {
        $calendars = $this->getCalendars();
        $calendars_select_options = array('All Calendars'=>'All Calendars');
        foreach($calendars as $calendar){
            $calendars_select_options[$calendar->id] = $calendar->name;
        }

        return [
            'eventpage' => [
                'title'       => 'fszteam.manageevents::lang.components.event_list.properties.eventpage.title',
                'description' => 'fszteam.manageevents::lang.components.event_list.properties.eventpage.description',
                'type'        => 'dropdown' // @todo Page picker
            ],
            'style' => [
                'description' => 'fszteam.manageevents::lang.components.event_list.properties.style.description',
                'title'       => 'fszteam.manageevents::lang.components.event_list.properties.style.title',
                'default'     => 'simple_list',
                'type'        => 'dropdown',
                'options'     => [
                    'simple_list'=>'fszteam.manageevents::lang.components.event_list.properties.style.options.simple',
                    'footer_list'=>'fszteam.manageevents::lang.components.event_list.properties.style.options.footer',
                    'show_schedule'=>'fszteam.manageevents::lang.components.event_list.properties.style.options.show',
                    'month_highlights'=>'fszteam.manageevents::lang.components.event_list.properties.style.options.month'
                ]
            ],
            'pastevents' => [
                'description' => 'fszteam.manageevents::lang.components.event_list.properties.pastevents.description',
                'title'       => 'fszteam.manageevents::lang.components.event_list.properties.pastevents.title',
                'type'        => 'dropdown',
                'options'     => [
                    'none'=>'fszteam.manageevents::lang.components.event_list.properties.pastevents.options.none',
                    'only'=>'fszteam.manageevents::lang.components.event_list.properties.pastevents.options.only',
                    'one'=>'fszteam.manageevents::lang.components.event_list.properties.pastevents.options.one',
                    'two'=>'fszteam.manageevents::lang.components.event_list.properties.pastevents.options.two',
                    'three'=>'fszteam.manageevents::lang.components.event_list.properties.pastevents.options.three'
                ]
            ],
            'calendar' => [
                'description' => 'fszteam.manageevents::lang.components.event_list.properties.calendar.description',
                'title'       => 'fszteam.manageevents::lang.components.event_list.properties.calendar.title',
                'default'     => 'All Calendars',
                'type'        => 'dropdown',
                'options'     => $calendars_select_options
            ],
            'num' => [
                'description' => 'fszteam.manageevents::lang.components.event_list.properties.num.description',
                'title'       => 'fszteam.manageevents::lang.components.event_list.properties.num.title',
                'default'     => '5',
                'type'        => 'string'
            ],
            'pagination' => [
                'description' => 'fszteam.manageevents::lang.components.event_list.properties.pagination.description',
                'title'       => 'fszteam.manageevents::lang.components.event_list.properties.pagination.title',
                'type'        => 'checkbox'
            ]
        ];
    }

    public function getCalendars(){
        return CalendarModel::get()->all();
    }

    public function onRun()
    {
        if($this->property('style') == 'month_highlights') {
            $this->monthDate = Carbon::now()->addMonths(Input::get('month'))->format('Y-m-d');
        }

        $this->page['events'] = $this->listEvents();
        $this->page['event_list_view'] = $this->alias.'::'.$this->property('style');
        $this->page['eventpage'] =  $this->property('eventpage');
        $this->page['pagination'] = $this->property('pagination');

        $settings = ManageEventsSettingsModel::instance();

        $this->page['PE_DATE_GENERIC'] = ($settings->get('pe_date_generic')) ? $settings->get('pe_date_generic') : 'Y-m-d';
        $this->page['PE_DATE_FULL'] = ($settings->get('pe_date_full')) ? $settings->get('pe_date_full') : 'Y-m-d g:i a';
        $this->page['PE_DATE_SPOKEN'] = ($settings->get('pe_date_spoken')) ? $settings->get('pe_date_spoken') : 'l M jS, Y';
        $this->page['PE_DATE_TIME'] = ($settings->get('pe_date_time')) ? $settings->get('pe_date_time') : 'g:i a';

        $this->addCss('/plugins/fszteam/manageevents/assets/css/manageevents_list.css');
    }

    public function getEventpageOptions()
    {
        $ParentOptions = array(''=>'-- chose one --');
        $pages = Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');

        $ParentOptions = array_merge($ParentOptions, $pages);

        //\Log::info($ParentOptions);
        return $ParentOptions;
    }


    /*
     * @getItemDates
     * - get grouped dates array
     */
    public function getItemDates($group,$event_id){
        $dates_string = '';
        $events = new GeneratedDateModel();
        $grouped_events = $events->getGroupedDates($event_id,$group);
        foreach($grouped_events  as $e){
            $dates_string .= date($this->page['PE_DATE_SPOKEN'],strtotime($e->event_date)).' | ';
            if($e->allday > 0){
                $dates_string .= 'All Day <br/>';
            }else{
                $dates_string .= date($this->page['PE_DATE_TIME'],strtotime($e->sttime)).' - '.date($this->page['PE_DATE_TIME'],strtotime($e->entime)).'<br/>';
            }
        }


        return $dates_string;
    }


    /*
     * @getFromToDates
     * - get condensed grouped dates array
     */
    public function getFromToDates($group,$event_id){
        $dates_string = '';
        $events = new GeneratedDateModel();
        $grouped_events = $events->getGroupedDates($event_id,$group);
        $ec = 0;
        foreach($grouped_events  as $e){
            $ec++;
            if($ec == 1){
                $dates_string .= date($this->page['PE_DATE_SPOKEN'],strtotime($e->event_date)).' - ';
            }elseif($ec == count($grouped_events)){
                $dates_string .= date($this->page['PE_DATE_SPOKEN'],strtotime($e->event_date)).' | ';
            }
            if($e->allday > 0){
                $time = 'All Day <br/>';
            }else{
                $time = date($this->page['PE_DATE_TIME'],strtotime($e->sttime)).' - '.date($this->page['PE_DATE_TIME'],strtotime($e->entime)).'<br/>';
            }
        }


        return $dates_string.$time;
    }

    public function listEvents()
    {
        $this->page['eventpage'] =  $this->property('eventpage');

        if ($this->events !== null)
            return $this->events;

        $calendar_ids = null;

        /*
        * if we have a calendar property
        * and it's not 'All Calendars'
        * assemble calendar ID filters
        */
        if($this->property('calendar') && $this->property('calendar') != 'All Calendars'){
            $calendars = explode(',',$this->property('calendar'));
            $calendar_ids =  '(';
            $ci = 0;
            foreach($calendars as $id){
                if($ci>0){ $calendar_ids .= ' OR '; }
                $calendar_ids .=  "calendar_id = '$id'";
                $ci++;
            }
            $calendar_ids .=  ')';
        }

        $events = new GeneratedDateModel();
        $events->calendarIds = $calendar_ids;
        $events->pastEvents = $this->property('pastevents');

        /*
        * return list of events
        */
        return $this->events = $events->getEventsList($this->property('num'), $this->monthDate);
    }

    public function onRefreshList()
    {
        $this->onRun();
        $this->page['month'] = Input::get('month');
    }
}
