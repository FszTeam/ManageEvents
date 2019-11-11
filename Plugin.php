<?php namespace FszTeam\ManageEvents;

use Backend;
use System\Classes\PluginBase;
use System\Classes\PluginManager;
use Backend\Models\User as BackendUserModel;
use FszTeam\ManageEvents\Models\Calendar;
use Event;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'fszteam.manageevents::lang.plugin.name',
            'description' => 'fszteam.manageevents::lang.plugin.description',
            'author'      => '://fszteam',
            'icon'        => 'icon-calendar-o'
        ];
    }

    public function boot()
    {
        Event::listen('pages.menuitem.listTypes', function() {
                return [
                    'manageevents-calendar'=>'fszteam.manageevents::lang.menuitems.calendar',
                    'all-manageevents-calendars'=>'fszteam.manageevents::lang.menuitems.allcalendars',
                ];
            });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
                if ($type == 'manageevents-calendar' || $type == 'all-manageevents-calendars')
                    return Calendar::getMenuTypeInfo($type);
            });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
                if ($type == 'manageevents-calendar' || $type == 'all-manageevents-calendars')
                    return Calendar::resolveMenuItem($item, $url, $theme);
            });
    }

    public function register ()
    {
        Event::listen('system.console.mirror.extendPaths', function($paths) {
            $paths->wildcards = array_merge($paths->wildcards, [
                'plugins/fszteam/manageevents/assets/*',
                'plugins/fszteam/manageevents/assets/*/*',
            ]);
        });
    }

    public function registerComponents()
    {
        return [
            'FszTeam\ManageEvents\Components\Event' => 'proEvent',
            'FszTeam\ManageEvents\Components\EventList' => 'proEventList',
            'FszTeam\ManageEvents\Components\EventCalendar' => 'proEventCalendar',
        ];
    }

    public function registerPageSnippets()
    {
        return [
            'FszTeam\ManageEvents\Components\EventList' => 'proEventList',
            'FszTeam\ManageEvents\Components\EventCalendar' => 'proEventCalendar',
        ];
    }

    public function registerNavigation()
    {
        return [
            'manageevents' => [
                'label'       => 'fszteam.manageevents::lang.plugin.name',
                'url'         => Backend::url('fszteam/manageevents/events'),
                'icon'        => 'icon-calendar',
                'permissions' => ['fszteam.manageevents.*'],
                'order'       => 500,

                'sideMenu' => [
                    'events' => [
                        'label'       => 'fszteam.manageevents::lang.sidemenu.events',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('fszteam/manageevents/events'),
                        'permissions' => ['fszteam.manageevents.access_events'],
                    ],
                    'generatedDates' => [
                        'label'       => 'fszteam.manageevents::lang.sidemenu.generated',
                        'icon'        => 'icon-list-alt',
                        'url'         => Backend::url('fszteam/manageevents/generateddates'),
                        'permissions' => ['fszteam.manageevents.access_events'],
                    ],
                    'calendars' => [
                        'label'       => 'fszteam.manageevents::lang.sidemenu.calendars',
                        'icon'        => 'icon-calendar-o',
                        'url'         => Backend::url('fszteam/manageevents/calendars'),
                        'permissions' => ['fszteam.manageevents.access_calendars'],
                    ],
                ]

            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'fszteam.manageevents.access_calendars' => ['label' => 'fszteam.manageevents::lang.permissions.managecalendars', 'tab' => 'ProEvents'],
            'fszteam.manageevents.access_events' => ['label' => 'fszteam.manageevents::lang.permissions.ManageEvents', 'tab' => 'ProEvents'],
            'fszteam.manageevents.access_proevent_settings' => ['label' => 'fszteam.manageevents::lang.permissions.managesettings', 'tab' => 'ProEvents']
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'ProEvents',
                'description' => 'fszteam.manageevents::lang.settings.description',
                'icon'        => 'icon-calendar',
                'class'       => 'FszTeam\ManageEvents\Models\Settings',
                'order'       => 100,
                'permissions' => ['fszteam.manageevents.access_proevent_settings']
            ]
        ];
    }

    public function registerFormWidgets()
	{
	    return [
	        'FszTeam\ManageEvents\Modules\Backend\Formwidgets\Multidate' => [
	            'label' => 'MultiDate',
	            'code' => 'multidate'
	        ],
            'FszTeam\ManageEvents\Modules\Backend\Formwidgets\Time' => [
                'label' => 'Time',
                'code' => 'time'
            ],
            'FszTeam\ManageEvents\Modules\Backend\Formwidgets\Rcolorpicker' => [
                'label' => 'Colorpicker',
                'code' => 'rcolorpicker'
            ]
	    ];
	}

    public function registerMarkupTags()
    {
        // Check the translate plugin is installed
        $filters = array();
        if (!PluginManager::instance()->exists('RainLab.Translate')) {
            $filters['_'] = ['Lang', 'get'];
            $filters['__'] = ['Lang', 'choice'];
        }

        return [
            'functions' => [
                'getAuthorInfo' => [$this, 'getAuthorInfo']
            ],
            'filters' => $filters
        ];
    }
    

    public function getAuthorInfo($id)
    {
        $user = BackendUserModel::where('id',$id)->first();
        if($user->avatar){
            $user->image = $user->avatar->getThumb(100, 100, ['mode' => 'crop']);
        }
        return $user;
    }

}
