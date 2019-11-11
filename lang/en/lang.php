<?php

return [
    'plugin' => [
        'name' => 'ProEvents',
        'description' => 'A powerful events platform.',
    ],
    'sidemenu' => [
        'events' => 'Events',
        'generated' => 'Generated Dates',
        'calendars' => 'Calendars'
    ],
    'menuitems' => [
        'calendar'=>'ProEvents Calender',
        'allcalendars'=>'All ProEvents Calendars'
    ],
    'permissions'=>[
        'managecalendars'=>'Manage Calendars',
        'ManageEvents'=>'Manage Events',
        'managesettings'=>'Manage ProEvent Settings'
    ],
    'settings' => [
        'description' => 'Manage ProEvents Settings.',
        'tabs' => [
            'render' => 'Render Page',
            'editor' => 'Editor',
            'social' => 'Social',
            'api' => 'API Settings',
            'authentication' => 'Authorize'
        ],
        'labels' => [
            'render' => 'Choose a default page that will render your ProBlog Posts',
            'editor' => 'Enable Markdown Editor / Preview',
            'parent' => 'Choose a default Blog Parent Page.',
            'sharethis' => 'ShareThis API Key',
            'facebook' => 'Enable Facebook Sharing?',
            'twitter' => 'Enable Twitter Sharing?',
            'google' => 'Enable Google Sharing?',
            'embedly' => 'Embed.ly API Key',
            'alchemy' => 'Alchemy API Key',
            'twitterauth' => 'Twitter Authentication'
        ]
    ],
    'backend'=>[
        'form' => [
            'cancel'=>'Cancel',
            'saveclose'=>'Save and Close',
            'or'=>'or',
            'create'=>'Create',
            'createclose'=>'Create and Close',
            'save'=>'Save',
            'update'=>'Update',
            'updateclose'=>'Update and Close'
        ],
        'toolbar' => [
            'duplicate'=>'Duplicate',
            'delete'=>'Delete'
        ],
        'calendars'=>[
            'form'=>[
                'title'=>'Calendars',
                'confirmdelete'=>'Do you really want to delete this Calendar?',
                'back'=>'Return to Calendar List'
            ],
            'toolbar'=>[
                'create'=>'Create Calendar',
                'edit'=>'Edit Calendar',
                'toolbarnew'=>'New Calendar'
            ],
            'columns'=>[
                'nocolor'=>'none',
                'name'=>'Name',
                'color'=>'Color'
            ],
            'fields'=>[
                'name'=>'Name',
                'slug'=>'Slug',
                'color'=>'Calendar Color'
            ]
        ],
        'events'=>[
            'form'=>[
                'title'=>'Events',
                'confirmdelete'=>'Do you really want to delete this Event?',
                'back'=>'Return to Events List'
            ],
            'toolbar'=>[
                'create'=>'Create Event',
                'edit'=>'Edit Event',
                'toolbarnew'=>'New Event'
            ],
            'columns'=>[
                'title'=>'Title',
                'author'=>'Author',
                'calendar'=>'Calendar',
                'date'=>'Date'
            ],
            'fields'=>[
                'tabs'=>[
                    'content'=>'Event Content',
                    'preview'=>'Preview',
                    'additional'=>'Additional Info',
                    'date'=>'Date Info',
                    'exclude'=>'Exclude Dates',
                    'booking'=>'Booking'
                ],
                'title'=>'Event Title',
                'content'=>'Event Details',
                'excerpt'=>'Excerpt',
                'locname'=>'Location Name',
                'locaddress'=>'Location Address',
                'email'=>'Contact Email',
                'date'=>'Event Date',
                'dates'=>'Event Dates',
                'sttime'=>'Start Time',
                'entime'=>'End Time',
                'recur'=>'Recuring',
                'recuroptions'=>[
                    'none'=>'None',
                    'daily'=>'Daily',
                    'weekly'=>'Weekly',
                    'everyother'=>'Every other Week',
                    'monthly'=>'Monthly',
                    'yearly'=>'Yearly'
                ],
                'thru'=>'Thru',
                'calendar'=>'Calendar',
                'allday'=>'All Day Event?',
                'grouped'=>'Event Dates Are Grouped?',
                'images'=>'Featured Images',
                'exclude'=>'Excluded Dates',
                'qty'=>'QTY',
                'price'=>'Event Price',
                'status'=>'Event Status',
                'statusoptions'=>[
                    'none'=>'None',
                    'available'=>'Available',
                    'booked'=>'Booked'
                ],
                'update'=>'Push Changes To Generated Dates?',
                'updateoptions'=>[
                    'passive'=>'Passive - Do not update',
                    'update'=>'Update - Update All Dates'
                ],
                'placeholders'=>[
                    'none'=>'None',
                    'chose'=>'- Chose -'
                ]
            ]
        ],
        'generated'=>[
            'form'=>[
                'title'=>'Generated Dates',
                'confirmdelete'=>'Do you really want to delete this date?',
                'back'=>'Return to Events List'
            ],
            'toolbar'=>[
                'edit'=>'Edit Generated Event Date'
            ],
            'columns'=>[
                'title'=>'Title',
                'date'=>'Date',
                'sttime'=>'Start Time',
                'entime'=>'End Time',
                'qty'=>'QTY',
                'status'=>'Status'
            ],
        ],
        'settings'=>[
            'fields'=>[
                'tabs'=>[
                    'social'=>'Social Settings',
                    'date'=>'Date/Time Settings'
                ],
                'sharethis'=>'ShareThis API Key',
                'twitter'=>'Enable Twitter Sharing?',
                'facebook'=>'Enable Facebook Sharing?',
                'google'=>'Enable Google Sharing?',
                'date_generic'=>'Date Generic Formatting',
                'date_full'=>'Date Full Formatting',
                'date_spoken'=>'Date Spoken Formatting',
                'date_time'=>'Date Time Formatting',
                'date_picker'=>'Datepicker Formatting',
                'date_backend'=>'Backend Date Picker Formatting',
                'date_jquery_description'=>'jQuery Date Formatting Standard',
                'date_moment_description'=>'moment.js Date Formatting Standard',
                'date_php_description'=>'PHP Date Formattig Standard'
            ]
        ]
    ],
    'components'=>[
        'event'=>[
            'details' => [
                'name' => 'Event',
                'description' => 'Displays your Event on a page.'
            ],
            'properties' => [
                'event_id' => [
                    'title' => 'ID parameter',
                    'description'=>'The URL route parameter used for looking up the post by its ID or slug.'
                ],
                'invites' => [
                    'title' => 'Enable Invites?',
                    'description'=>'Allow users to email a friend and invite them'
                ]
            ]
        ],
        'event_calendar'=>[
            'details' => [
                'name' => 'Event Calendar',
                'description' => 'Displays a calendar of Events on a page.'
            ],
            'properties' => [
                'eventpage' => [
                    'title' => 'Event Details Page',
                    'description'=>'Page name to use for clicking on an Event.',
                    'options'=>[
                        'chose'=>'-- chose one --'
                    ]
                ],
                'style' => [
                    'title' => 'Calendar Style',
                    'description'=>'What Calendar View?',
                    'options'=>[
                        'responsive'=>'Responsive',
                        'jquery'=>'jQuery Fullcalendar',
                        'smallcal'=>'Small Calendar'
                    ]
                ],
                'calendar' => [
                    'title' => 'Calendar',
                    'description'=>'Which Calendar?',
                    'options'=>[
                        'all_calendars'=>'All Calendars'
                    ]
                ],
                'euro' => [
                    'title' => 'European Format',
                    'description'=>'European Date Formatting?'
                ],
                'ical' => [
                    'title' => 'iCal',
                    'description'=>'Show iCal Link?'
                ]
            ]
        ],
        'event_list'=>[
            'details' => [
                'name' => 'Event List',
                'description' => 'Displays a list of Events on a page.'
            ],
            'properties' => [
                'eventpage' => [
                    'title' => 'Event Details Page',
                    'description'=>'Page name to use for clicking on an Event.',
                    'options'=>[
                        'chose'=>'-- chose one --'
                    ]
                ],
                'style' => [
                    'title' => 'List Style',
                    'description'=>'What List View?',
                    'options'=>[
                        'simple'=>'Simple',
                        'footer'=>'Footer',
                        'show'=>'Show Schedule',
                        'month'=>'Month Highlights'
                    ]
                ],
                'pastevents' => [
                    'title' => 'Past Events',
                    'description'=>'Show only events that have passed?',
                    'options'=>[
                        'none'=>'Do not show past events',
                        'only'=>'Show only past events',
                        'one'=>'Show only one day of past events',
                        'two'=>'Show two days of past events',
                        'three'=>'Show three days of past events'
                    ]
                ],
                'calendar' => [
                    'title' => 'Calendar',
                    'description'=>'Which Calendar?',
                    'options'=>[
                        'all_calendars'=>'All Calendars'
                    ]
                ],
                'num' => [
                    'title' => 'Number of Events',
                    'description'=>'Number of Events to List?'
                ],
                'pagination' => [
                    'title' => 'Pagination',
                    'description'=>'Show Pagination?'
                ]
            ]
        ]
    ]
];
