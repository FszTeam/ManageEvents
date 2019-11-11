<?php namespace FszTeam\ManageEvents\Modules\Backend\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * Colorpicker
 * Renders a code editor field.
 *
 * @package fszteam\backend
 * @author ChadStrat
 */
class Rcolorpicker extends FormWidgetBase
{
    /**
     * {@inheritDoc}
     */
    public $defaultAlias = 'rcolorpicker';


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
        return $this->makePartial('rcolorpicker');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['value'] = $this->model->{$this->fieldName};
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addCss('css/colpick.css');
        $this->addJs('js/colpick.js');
        $this->addJs('js/initialize.js');
    }
    
    /** 
    * Process the postback data for this widget. 
    * @param $value The existing value for this widget. 
    * @return string The new value for this widget. 
    */ 

    public function getSaveData($value) 
    { 
        return $value; 
    } 


}