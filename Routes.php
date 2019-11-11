<?php
use FszTeam\ManageEvents\Classes\IcalManager;

Route::group(['prefix' => 'fszteam_api/events/ical/'], function() {

    Route::get('all', function(){ return View::make('fszteam.manageevents::ical.all', ['events' => IcalManager::getAllEvents()]); });
    Route::get('calendar', function(){ return View::make('fszteam.manageevents::ical.calendar', []); });
    Route::get('event', function(){ return View::make('fszteam.manageevents::ical.event', []); });

});
?>