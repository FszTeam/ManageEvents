<?php namespace FszTeam\ManageEvents\Models;

use DB;
use Str;
use Model;
use BackendAuth;
use Backend\Models\User as BackendUser;
use October\Rain\Database\ModelException;
use FszTeam\ManageEvents\Classes\MultidateHelper;
use FszTeam\ManageEvents\Models\GeneratedDate as GeneratedDateModel;
use System\Classes\PluginManager;

class Event extends Model
{
    use \October\Rain\Database\Traits\Purgeable;

    public $table = 'fszteam_events';

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /**
     * @var array Attributes that support translation, if available.
     */
    public $translatable = [
        'title',
        'excerpt',
        'content',
        'location_name',
        'location_address',
        'contact_email'
    ];

    public $purgeable = [
    	'update_generated_booking_info'
    ];

    /*
     * Validation
     */
    public $rules = [
        'title' => 'required',
        'excerpt' => 'required',
        'calendar_id' => 'required'
    ];

    public $customMessages = [
       'calendar_id.required' => 'You must select a Calendar!',
    ];

    protected $guarded = [];

    protected $jsonable = ['multidate','excluded'];

    /*
     * Relations
     */
    public $belongsTo = [
        'user' => ['Backend\Models\User'],
        'calendar' => ['FszTeam\ManageEvents\Models\Calendar', 'table' => 'fszteam_events_calendars', 'order' => 'name']
    ];

    public $attachMany = [
        'featured_images' => ['System\Models\File']
    ];

	public $hasMany = [
		'generatedDates' => ['FszTeam\ManageEvents\Models\GeneratedDate']
	];

    public function beforeValidate()
    {
        // Generate a URL slug for this model
        if (!$this->exists && !$this->slug)
            $this->slug = Str::slug($this->title);
    }

    public function beforeDelete()
    {
        $this->cleanTranslation();

        GeneratedDateModel::where('event_id', '=', $this->id)->delete();
    }

	public function beforeSave(){
        if (!$this->exists && !$this->slug)
            $this->slug = Str::slug($this->title);

		$_REQUEST['update_generated'] = $_REQUEST['Event']['update_generated_booking_info'];
	}

	public function afterSave(){

        $user = BackendAuth::getUser();
		Event::where('id',$this->id)->update(array('user_id'=>$user->id));

		/*
		* generate dates array from inputs
		*/
		$dates = new MultidateHelper($this->multidate,$this->recur,$this->thru);
		$dates_array = $dates->getDates();
		$data = ['date'=>null];
		$grouped = 0;
		$gt = 0;

		$exclude_dates = json_decode($this->excluded, true);
        $originalDateSet = json_decode($this->multidate);

		$exclude_dates = $exclude_dates['date'];
		if(!is_array($exclude_dates)){
			$exclude_dates = array();
		}else{
			$simple_excludes = array();
			foreach($exclude_dates as $exdate){
				$simple_excludes[] = date('Y-m-d',strtotime($exdate));
			}
		}

		//\Log::info($originalDateSet->date);

        if($originalDateSet->date && count($originalDateSet->date) > 1 && ($originalDateSet->date[0] == $originalDateSet->date[1])) {
            $sameDayEvents = true;
        }else{
             $sameDayEvents = false;
        }

		/*
		* loop through dates array and create/update dates
		*/
		if(is_array($dates_array)){

			foreach($dates_array as $dategroup){
				if($this->grouped > 0){
					$gt = 0;
				}
				if(is_array($dategroup)){

					foreach($dategroup as $date){

                        if(!is_array($date)){
                            $date = array('date' => $date);
						}

						if(!in_array($date['date'],$simple_excludes)){

							if(!$sameDayEvents && $this->grouped < 1){
							//if(count($dates_array) > 1) {
								$grouped = $gt++;
							}

							/*
							* maintain Taxonomy & try and retrieve existing date ID
							*/
							$generated_date = GeneratedDateModel
								::where('event_id', '=', $this->id)
								->where('event_date','=',$date['date'])
								->where('sttime','=',date('H:i:s',strtotime($date['sttime'])))
								->first();

							$data = [
									'event_id' => $this->id,
									'calendar_id' => $this->calendar_id,
									'title' => $this->title,
									'content' => $this->content,
									'excerpt' => $this->excerpt,
									'event_date' => $date['date'],
									'sttime' => date('H:i:s',strtotime($date['sttime'])),
									'entime' => date('H:i:s',strtotime($date['entime'])),
									'allday' => $this->allday,
									'recur' => $this->recur,
									'grouped' => $this->grouped,
									'grouped_id' => $grouped,
									'thru' => $this->thru,
									'updated' => 1,
                                    'user_id' => $this->user_id
								];

							if($_REQUEST['update_generated']=='update'){
								$data['status'] = $this->status;
								$data['event_qty'] = $this->event_qty;
								$data['event_price'] = $this->event_price;
							}

							/* if we have an exeisting date
							* matching this event_id and group#
							* update it. else create new.
							*/
							if(is_object($generated_date)){
								$this->generatedDates()->where('id', '=', $generated_date->id)->update($data);
							}else{
								$this->generatedDates()->create($data);
							}
						}
					}
				}
			}
		}else{
			$date_info = json_decode($this->multidate);
			/*
			* maintain Taxonomy & try and retrieve existing date ID
			*/
			$generated_date = GeneratedDateModel
							::where('event_id', '=', $this->id)
							->where('event_date','=',$date_info->date[0])
							->where('sttime','=',date('H:i:s',strtotime($date_info->sttime[0])))
							->first();

			$data = [
					'event_id' => $this->id,
					'calendar_id' => $this->calendar_id,
					'title' => $this->title,
					'content' => $this->content,
					'excerpt' => $this->excerpt,
					'event_date' => $date_info->date[0],
					'sttime' => date('H:i:s',strtotime($date_info->sttime[0])),
					'entime' => date('H:i:s',strtotime($date_info->entime[0])),
					'allday' => $this->allday,
					'recur' => $this->recur,
					'grouped' => $this->grouped,
					'grouped_id' => 1,
					'thru' => $this->thru,
					'status' => null,
					'updated' => 1,
					'event_qty' => null,
					'event_price' => null,
                    'user_id' => $this->user_id
				];

			/* if we have an exeisting date
			* matching this event_id and group#
			* update it. else create new.
			*/
			if(is_object($generated_date)){
				$this->generatedDates()->where('id', '=', $generated_date->id)->update($data);
			}else{
				$this->generatedDates()->create($data);
			}
		}

		/*
		* remove all dates of this event id that have not been updated
		*/
		$this->generatedDates()->where('event_id', '=', $this->id)->where('updated', '<', 1)->delete();
		/*
		* reset all dates that have been updated
		*/
		$this->generatedDates()->where('event_id', '=', $this->id)->update(array('updated' => 0));

        $this->updateTranslation();
	}

    public function updateTranslation()
    {
        //only execute if Translate is installed
        if (PluginManager::instance()->exists('RainLab.Translate')) {
            /** Get all generated events related to this parent model **/
            $generated_date = GeneratedDateModel
                ::where('event_id', '=', $this->id)
                ->get()->all();

            /** get the parent model translations **/
            $translate_origin = DB::select('select * from rainlab_translate_attributes where model_type = ? and model_id = ?', ['FszTeam\ManageEvents\Models\Event',$this->id]);

            /** loop through each generated date **/
            foreach($generated_date as $genDate){
                /** and for each of those also loop through each origin tranlsation locale **/
                foreach($translate_origin as $tran){
                    $attribute_data = json_decode($tran->attribute_data);
                    $data = [
        					'title' => $attribute_data->title,
        					'content' => $attribute_data->content,
        					'excerpt' => $attribute_data->excerpt
        				];

                    $tran_data = [
                        'locale' => $tran->locale,
                        'model_id' => $genDate->id,
                        'model_type' => 'FszTeam\\ManageEvents\\Models\\GeneratedDate',
                        'attribute_data' => json_encode($data)
                    ];
						
					//wipe any translations as the instance criteria may have changed
                    Db::table('rainlab_translate_attributes')
                        ->where('model_type', '=', 'FszTeam\\ManageEvents\\Models\\GeneratedDate')
                        ->where('model_id', '=', $genDate->id)
                        ->where('locale','=',$tran->locale)
                        ->delete();

                    //create new translation instances
                    $translate = Db::table('rainlab_translate_attributes')->insert($tran_data);

                    //\Log::info(json_encode($translate));
                }
            }
        }
    }

    public function cleanTranslation()
    {
        //only execute if Translate is installed
        if (PluginManager::instance()->exists('RainLab.Translate')) {
            /** first remove the parent event translation **/
            Db::table('rainlab_translate_attributes')
                ->where('model_type', '=', 'FszTeam\\ManageEvents\\Models\\Event')
                ->where('model_id', '=', $this->id)
                ->delete();
            /** Get all generated events related to this parent model **/
            $generated_date = GeneratedDateModel
                ::where('event_id', '=', $this->id)
                ->get()->all();

            /** loop through each generated date **/
            foreach($generated_date as $genDate){
                //wipe any translations as the instance criteria may have changed
                Db::table('rainlab_translate_attributes')
                    ->where('model_type', '=', 'FszTeam\\ManageEvents\\Models\\GeneratedDate')
                    ->where('model_id', '=', $genDate->id)
                    ->delete();
            }
        }
    }
}
