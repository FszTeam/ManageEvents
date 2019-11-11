<?php namespace FszTeam\ManageEvents\Modules\Backend\FormWidgets;

use Backend\Classes\FormWidgetBase;
use FszTeam\ManageEvents\Models\Settings as ManageEventsSettingsModel;
/**
 * Code Editor
 * Renders a code editor field.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Multidate extends FormWidgetBase
{
    /**
     * {@inheritDoc}
     */
    public $defaultAlias = 'multidate';

    /**
     * @var bool Display mode: datetime, date, time.
     */
    public $mode = 'datetimetime';

    /**
     * @var string the minimum/earliest date that can be selected.
     */
    public $minDate = '2000-01-01';

    /**
     * @var string the maximum/latest date that can be selected.
     */
    public $maxDate = '2020-12-31';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->mode = $this->getConfig('mode', $this->mode);
        $this->minDate = $this->getConfig('minDate', $this->minDate);
        $this->maxDate = $this->getConfig('maxDate', $this->maxDate);
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('multidate');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $settings = ManageEventsSettingsModel::instance();

        $this->vars['PE_DATE_BACKEND'] = ($settings->get('pe_date_backend')) ? $settings->get('pe_date_backend') : 'MM/DD/YY';
        $this->vars['PE_DATE_TIME'] = ($settings->get('pe_date_time')) ? $settings->get('pe_date_time') : 'g:i a';

        if($this->mode == 'date'){
            $dates_string = $this->model->{$this->fieldName} ?: null;
        }else{
            $dates_string = $this->model->{$this->fieldName} ?: array('date'=>array(date('Y-m-d')),'sttime'=>array(date('H:ia')),'entime'=>array(date('H:ia')));
        }
    	$dates = (!is_array($dates_string)) ? json_decode($dates_string,true) : $dates_string;

        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $dates;
        $this->vars['showTime'] = $this->mode == 'time' || $this->mode == 'datetime';
        $this->vars['minDate'] = $this->minDate;
        $this->vars['maxDate'] = $this->maxDate;
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addCss('css/datepicker.css');
        $this->addCss('css/timepicker.css');
        $this->addJs('js/timepicker.js');
    }

	/**
	* Process the postback data for this widget.
	* @param $value The existing value for this widget.
	* @return string The new value for this widget.
	*/

	public function getSaveValue($value)
	{
		//\Log::info(json_encode($value));
		return json_encode($value);
    }
    
    public function getFormattedDate($date)
    {
        
    }


}
