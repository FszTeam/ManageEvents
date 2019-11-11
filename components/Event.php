<?php namespace FszTeam\ManageEvents\Components;

use Mail;
use Auth;
use System\Classes\PluginManager;
use Flash;
use Cms\Classes\ComponentBase;
use FszTeam\ManageEvents\Models\GeneratedDate  as GeneratedDateModel;
use FszTeam\ManageEvents\Models\Event  as EventModel;
use FszTeam\ManageEvents\Models\Settings as ManageEventsSettingsModel;
use Request;

class Event extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'fszteam.manageevents::lang.components.event.details.name',
            'description' => 'fszteam.manageevents::lang.components.event.details.description',
            'event_id' => ':event_id'
        ];
    }

    public function defineProperties()
    {
        return [
            'event_id' => [
                'description' => 'fszteam.manageevents::lang.components.event.properties.event_id.description',
                'title'       => 'fszteam.manageevents::lang.components.event.properties.event_id.title',
                'default'     => ':event_id',
                'type'        => 'string'
            ],
            'invites' => [
                'description' => 'fszteam.manageevents::lang.components.event.properties.invites.description',
                'title'       => 'fszteam.manageevents::lang.components.event.properties.invites.title',
                'type'        => 'checkbox'
            ]
        ];
    }

    public function getItemDates($group,$event_id){
        $dates_string = '';
        $grouped_events = json_decode(GeneratedDateModel
                        ::where('event_id', '=', $event_id)
                        ->where('grouped_id', '=', $group)
                        ->orderBy('event_date')
                        ->orderBy('sttime','asc')
                        ->take(30)
                        ->get());

        foreach($grouped_events  as $e){
            $dates_string .= date($this->page['PE_DATE_SPOKEN'] ,strtotime($e->event_date)).' | ';
            if($e->allday > 0){
                $dates_string .= 'All Day <br/>';
            }else{
                $dates_string .= date($this->page['PE_DATE_TIME'] ,strtotime($e->sttime)).' - '.date($this->page['PE_DATE_TIME'] ,strtotime($e->entime)).'<br/>';
            }
        }


        return $dates_string;
    }

    public function onEmailFriend(){

        $event = $this->getEvent();

        if($event->grouped > 0)
        {
            $details = $this->getItemDates($event->grouped_id,$event->event_id);
        }else{
            $details = date($this->page['PE_DATE_SPOKEN'] ,strtotime($event->event_date)).' | ';
            if($event->allday > 0)
            {
                $details .= 'All Day';
            }else{
                $details .= date($this->page['PE_DATE_TIME'] ,strtotime($event->sttime)).' - '.date($this->page['PE_DATE_TIME'] ,strtotime($event->entime));
            }
        }

        $data = array(
        'title' => $event->name,
        'details' => $details,
        'email'=> $_REQUEST['invite_email'],
        'url' => Request::url()
        );

        Mail::send('fszteam.manageevents::emails.event_invite', $data, function($message) use ($data)
        {
            $message->to($data['email']);

            $emails = $message->getBody();
            \Log::info("Pretending to mail message: {$emails}");
        });

        Flash::success(post('flash', 'Email sent successfully!'));
    }

     /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!PluginManager::instance()->exists('RainLab.User'))
            return null;

        if (!Auth::check())
            return null;

        return Auth::getUser();
    }


    public function onRun(){
        $this->page['event_item'] = $this->getEvent();

        $this->page['back'] = Request::header('referer');
        $this->page['url'] = Request::url();

        $this->addCss('/plugins/fszteam/manageevents/assets/css/manageevents_event.css');
        $this->addCss('/plugins/fszteam/manageevents/assets/css/manageevents_modal.css');
        $this->addJs('/plugins/fszteam/manageevents/assets/js/manageevents_modal.js');

        $settings = ManageEventsSettingsModel::instance();
        $this->page['sharethis'] = $settings->get('sharethis');
        $this->page['facebook'] = $settings->get('facebook');
        $this->page['twitter'] = $settings->get('twitter');
        $this->page['google'] = $settings->get('google');

        $this->page['PE_DATE_GENERIC'] = ($settings->get('pe_date_generic')) ? $settings->get('pe_date_generic') : 'Y-m-d';
        $this->page['PE_DATE_FULL'] = ($settings->get('pe_date_full')) ? $settings->get('pe_date_full') : 'Y-m-d g:i a';
        $this->page['PE_DATE_SPOKEN'] = ($settings->get('pe_date_spoken')) ? $settings->get('pe_date_spoken') : 'l M jS, Y';
        $this->page['PE_DATE_TIME'] = ($settings->get('pe_date_time')) ? $settings->get('pe_date_time') : 'g:i a';

        $this->page['user'] = $this->user();
        $this->page['invites'] = $this->property('invites');

    }

    public function getEvent(){
        if ($this->event !== null)
            return $this->event;

        if (!$this->param('event_id'))
            return null;

        //\Log::info(json_encode($this->param('event_id')));
        return $this->event = GeneratedDateModel::where('id','=',$this->param('event_id'))->first();
    }
}
