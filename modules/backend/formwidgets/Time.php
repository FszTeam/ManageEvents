<?php namespace FszTeam\ManageEvents\Modules\Backend\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * Time Picker
 *
 * @package fszteam\manageevents
 * @author ChadStrat
 */
class Time extends FormWidgetBase
{
    /**
     * {@inheritDoc}
     */
    public $defaultAlias = 'time';

    /**
     * {@inheritDoc}
     */
    public function init()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('time');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $value = $this->model->{$this->fieldName} ?: date('H:ia');
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addCss('css/timepicker.css');

        $this->addJs('js/timepicker.js');
        $this->addJs('js/initialize.js');
    }
    
    /** 
    * Process the postback data for this widget. 
    * @param $value The existing value for this widget. 
    * @return string The new value for this widget. 
    */ 

    public function getSaveData($value) 
    { 
        //\Log::info(json_encode($value)); 
        return date('H:i:s',strtotime($value)); 
    } 


}