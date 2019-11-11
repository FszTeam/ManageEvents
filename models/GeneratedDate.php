<?php namespace FszTeam\ManageEvents\Models;

use Str;
use DB;
use Model;
use Carbon\Carbon;
use October\Rain\Database\ModelException;
use FszTeam\ManageEvents\Models\Settings as ManageEventsSettingsModel;

class GeneratedDate extends Model
{
    public $table = 'fszteam_generated_dates';

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    public $date;
    public $time;
    public $calendarIds;
    public $pastEvents;

    /**
     * @var array Attributes that support translation, if available.
     */
     public $translatable = [
         'title',
         'excerpt',
         'content'
     ];

    /*
     * Validation
     */
    public $rules = [];

    protected $guarded = [];

    /*
     * Relations
     */
    public $belongsTo = [
        'event' => ['FszTeam\ManageEvents\Models\Event'],
		'calendars' => ['FszTeam\ManageEvents\Models\Calendar', 'table'=> 'fszteam_events_calendars', 'key' => 'event_id']
    ];
    
    public function beforeSave()
    {
        $this->sttime = date('H:i:s',strtotime($this->sttime));
        $this->entime = date('H:i:s',strtotime($this->entime));
        $this->event_qty = $this->event_qty?$this->event_qty:0;
    }

    public function setDateTime($monthDate = null)
    {
        $settings = ManageEventsSettingsModel::instance();
        $dropoff = $settings->get('pe_dropoff');

        if($monthDate){
            $date = $monthDate;
        }else{
            $date = date("Y-m-d");
        }
        $time = date("H:i:s");
        
        if($dropoff) {
            $hms = explode(':',$dropoff);
            $backDated = date("Y-m-d H:i:s", strtotime("-$hms[0] Hours -$hms[1] Minutes -$hms[2] Seconds",strtotime($date . " " .$time)));
            $backDatedCombined = explode(' ',$backDated);
            $date = $backDatedCombined[0];
            $time = $backDatedCombined[1];
        }

        $this->date = $date;
        $this->time = $time;
    }


    /*
     * Get specific calendar event dates by month for calendar views
     */
    public function getCalendarEvents($month,$calendar_ids=null)
    {
        if($calendar_ids){
            $events = GeneratedDate
                ::whereRaw($calendar_ids)
                ->whereRaw("DATE_FORMAT(event_date,'%m') = $month")
                ->orderBy('event_date')
                ->orderBy('sttime','asc')
                ->get()->all();
        }else{
            $events = GeneratedDate
                ::whereRaw("DATE_FORMAT(event_date,'%m') = $month")
                ->orderBy('event_date')
                ->orderBy('sttime','asc')
                ->get()->all();
        }

        return $events;
    }


    /*
     * Get specific calendar event dates by start-end for calendar views
     */
    public function getFromToCalendarEvents($start,$end,$calendar_ids=null)
    {
        if($calendar_ids){
            $events = GeneratedDate
                ::whereRaw($calendar_ids)
                ->whereRaw("DATE_FORMAT(event_date,'%Y-%m-%d') >= DATE_FORMAT('$start','%Y-%m-%d')")
                ->whereRaw("DATE_FORMAT(event_date,'%Y-%m-%d') <= DATE_FORMAT('$end','%Y-%m-%d')")
                ->orderBy('event_date')
                ->orderBy('sttime','asc')
                ->get()->all();
        }else{
            $events = GeneratedDate
                ::whereRaw("DATE_FORMAT(event_date,'%Y-%m-%d') >= DATE_FORMAT('$start','%Y-%m-%d')")
                ->whereRaw("DATE_FORMAT(event_date,'%Y-%m-%d') <= DATE_FORMAT('$end','%Y-%m-%d')")
                ->orderBy('event_date')
                ->orderBy('sttime','asc')
                ->get()->all();
        }

        return $events;
    }

    /*
     * Get specific calendar event dates by month for list views
     */
    public function getCalendarEventsList($calendar_ids=null,$num=null)
    {
        $this->setDateTime();

        $events = GeneratedDate
            ::whereRaw($calendar_ids)
            ->whereRaw("((event_date = ? AND sttime > ?) OR event_date > ?)", array($this->date,$this->time,$this->date))
            ->groupBy('event_id','grouped_id')
            ->orderBy('event_date')
            ->orderBy('sttime','asc')
            ->paginate($num);

        return $events;
    }


    /*
     * Get event dates by for list views
     */
    public function getEventsList($num=null, $monthDate=null)
    {
        $this->setDateTime($monthDate);

        $query = GeneratedDate::groupBy('grouped_id','event_id');

        if(!is_null($this->calendarIds)) { 
            $query->whereRaw($this->calendarIds); 
        }

        if($monthDate) {
            $query->whereRaw("(MONTH(event_date) = MONTH(?))", array($monthDate));
        }else{
            switch ($this->pastEvents) {
                case 'none':
                    $query->whereRaw("event_date >= ?", array($this->date));
                    break;
                case 'only':
                    $query->whereRaw("((event_date = ? AND sttime < ?) OR event_date < ?)", array($this->date,$this->time,$this->date));
                    break;
                case 'one':
                    $newdate = date('Y-m-d', strtotime('-1 day',strtotime($this->date)));
                    $query->whereRaw("event_date >= ?", array($newdate));
                    break;
                case 'two':
                    $newdate = date('Y-m-d', strtotime('-2 days',strtotime($this->date)));
                    $query->whereRaw("event_date >= ?", array($newdate));
                    break;
                case 'three':
                    $newdate = date('Y-m-d', strtotime('-3 days',strtotime($this->date)));
                    $query->whereRaw("event_date >= ?", array($newdate));
                    break;
            }
        }

        //DB::enableQueryLog(); // Enable query log
        
        $events = $query
            ->orderBy('event_date','asc')
            ->orderBy('sttime','asc')
            ->paginate($num);

            
        //dd(DB::getQueryLog()); // Show results of log

        return $events;
    }

    /*
     * Get past event dates for list views
     */
    public function getPastEventsList($num=null)
    {
        $this->setDateTime();

        $events = GeneratedDate
            ::groupBy('grouped_id','event_id')
            ->whereRaw("((event_date = ? AND sttime < ?) OR event_date < ?)", array($this->date,$this->time,$this->date))
            ->orderBy('event_date', 'asc')
            ->orderBy('sttime','asc')
            ->paginate($num);

        return $events;
    }


    /*
     * Get grouped events for given eventID and groupID
     */
    public function getGroupedDates($event_id,$group)
    {
        return json_decode(GeneratedDate
                        ::where('event_id', '=', $event_id)
                        ->where('grouped_id', '=', $group)
                        ->orderBy('event_date')
                        ->orderBy('sttime','asc')
                        ->get());
    }

    /*
     * Check whether or not a given date has an event
     */
    public static function dayHasEvents($date,$calendar_ids=null)
    {
        if($calendar_ids){
            return GeneratedDate
                            ::whereRaw($calendar_ids)
                            ->whereRaw("DATE_FORMAT(event_date,'%Y-%m-%d') = DATE_FORMAT('$date','%Y-%m-%d')")
                            ->first();
        }else{
            return GeneratedDate
                            ::whereRaw("DATE_FORMAT(event_date,'%Y-%m-%d') = DATE_FORMAT('$date','%Y-%m-%d')")
                            ->first();
        }
    }
}
