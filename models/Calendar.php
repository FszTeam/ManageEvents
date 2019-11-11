<?php namespace FszTeam\ManageEvents\Models;

use Str;
use Model;
use Cms\Classes\Theme;
use Cms\Classes\Page as CmsPage;
use October\Rain\Router\Helper as RouterHelper;
use URL;

class Calendar extends Model
{
    public $table = 'fszteam_manageevents_calendars';

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /**
     * @var array Attributes that support translation, if available.
     */
    public $translatable = [
        'name'
    ];

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|between:3,64|unique:fszteam_manageevents_calendars',
        'code' => 'unique:fszteam_manageevents_calendars',
    ];

    protected $guarded = [];

    public function beforeValidate()
    {
        // Generate a URL slug for this model
        if (!$this->exists && !$this->slug)
            $this->slug = Str::slug($this->name);
    }

    public function beforeSave()
    {
        //\Log::info(json_encode($_REQUEST));
    }

    /*
     * Get specific calendarID by name
     */
    public static function selectCalendarByName($name)
    {
        $calendar_id = Calendar
                            ::select('id')
                            ->where('name',$name)
                            ->take(1)
                            ->get()->all();
        return $calendar_id;
    }

    public static function resolveMenuItem($item, $url, $theme)
    {

        if($item->type == 'manageevents-calendar'){
            $Item = Array (
                'url' => static::getCalendarRenderUrl($theme,$item),
                'isActive' => 1,
            );
        }else{
            $Item = Array (
                'url' => static::getCalendarRenderUrl($theme,$item),
                'isActive' => 1,
                'items' => static::getEventCalendarRenderUrls($theme,$item)
            );
        }

        return $Item;
    }

    public static function getMenuTypeInfo($type)
    {
        $calendars = Calendar::lists('slug', 'name');

        if($type == 'manageevents-calendar'){
            $item = Array (
                'dynamicItems'  => 0,
                'nesting'       => 0,
                'references'    => $calendars,
                'cmsPages'   => static::getCalendarRenderPages()
            );
        }else{
            $item = Array (
                'dynamicItems'  => true,
                'cmsPages'   => static::getCalendarRenderPages()
            );
        }
        return $item;
    }

    private static function getCalendarRenderUrl($theme, $item)
    {
        $calendar = Calendar::where('name',$item->reference)->first();
        $page = CmsPage::loadCached($theme, $item->cmsPage);

        // Always check if the page can be resolved
        if (!$page)
            return;

        $url = null;

        if(!$calendar){
            $options = ['calendar' => null];
        }else{
            $options = ['calendar' => $calendar->slug];
        }

        // Generate the URL
        $url = CmsPage::url($page->getBaseFileName(), $options , false);

        $url = URL::to(Str::lower(RouterHelper::normalizeUrl($url))).'/';

        return $url;
    }

    private static function getEventCalendarRenderUrls($theme, $item)
    {
        $page = CmsPage::loadCached($theme, $item->cmsPage);
        $result = [];
        $calendars = Calendar::lists('slug', 'name');

        $pages = [];

        foreach($calendars as $slug=>$name){
            $url = CmsPage::url($page->getBaseFileName(), ['calendar' => $slug], false);
            $url = URL::to(Str::lower(RouterHelper::normalizeUrl($url))).'/';
            $pages[] = array(
                'title'=>$name,
                'url'=>$url,
            );
        }

        return $pages;
    }

    private static function getCalendarRenderPages()
    {
        $result = [];

        $theme = Theme::getActiveTheme();
        $pages = CmsPage::listInTheme($theme, true);

        $cmsPages = [];
        foreach ($pages as $page) {
            if (!$page->hasComponent('proEventList') && !$page->hasComponent('proEventCalendar'))
                continue;

            $cmsPages[] = $page;
        }

        $cmsPages;

        return $cmsPages;
    }
}
