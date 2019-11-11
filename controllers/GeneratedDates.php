<?php namespace FszTeam\ManageEvents\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use FszTeam\ManageEvents\Models\Settings as ManageEventsSettingsModel;

class GeneratedDates extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('FszTeam.ManageEvents', 'manageevents', 'generatedDates');
    }

    public function listInjectRowClass($model, $definition)
    {
        $settings = ManageEventsSettingsModel::instance(); 
        $PE_DATE_GENERIC = ($settings->get('pe_date_generic')) ? $settings->get('pe_date_generic') : 'Y-m-d'; 
        $PE_DATE_SPOKEN = ($settings->get('pe_date_spoken')) ? $settings->get('pe_date_spoken') : 'l M jS, Y'; 
        $PE_DATE_TIME = ($settings->get('pe_date_time')) ? $settings->get('pe_date_time') : 'g:i a'; 

        $model->event_date = '('. date($PE_DATE_GENERIC,strtotime($model->event_date)) .') '.date($PE_DATE_SPOKEN,strtotime($model->event_date));
        $model->sttime = date($PE_DATE_TIME,strtotime($model->sttime));
        $model->entime = date($PE_DATE_TIME,strtotime($model->entime));
    }
}