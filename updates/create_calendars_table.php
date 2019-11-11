<?php namespace FszTeam\ManageEvents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCalendarsTable extends Migration
{

    public function up()
    {
        Schema::create('fszteam_manageevents_calendars', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->index();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->text('color')->nullable();
            $table->timestamps();
        });

        Schema::create('fszteam_events_calendars', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('event_id')->unsigned();
            $table->integer('calendar_id')->unsigned();
            $table->primary(['event_id', 'calendar_id']);
        });
    }

    public function down()
    {
        Schema::drop('fszteam_manageevents_calendars');
        Schema::drop('fszteam_events_calendars');
    }

}
