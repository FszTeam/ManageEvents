<?php namespace FszTeam\ManageEvents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateEventsTable extends Migration
{

    public function up()
    {
        Schema::create('fszteam_events', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('calendar_id')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->index();
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->text('multidate')->nullable();
            $table->text('excluded')->nullable();
            $table->integer('allday')->unsigned();
            $table->integer('grouped')->unsigned();
            $table->date('thru')->nullable();
            $table->text('recur')->nullable();
            $table->boolean('published')->default(false);
            $table->integer('event_qty')->unsigned()->nullable()->default(0);
            $table->text('event_price')->nullable();
            $table->text('status')->nullable();
            $table->timestamps();
        });
        
        Schema::create('fszteam_generated_dates', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('event_id')->unsigned()->index();
            $table->text('calendar_id')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->date('event_date')->nullable();
            $table->time('sttime')->nullable();
            $table->time('entime')->nullable();
            $table->integer('allday')->nullable();
            $table->text('recur')->nullable();
            $table->date('thru')->nullable();
            $table->integer('grouped')->nullable();
            $table->integer('grouped_id')->nullable();
            $table->integer('updated')->nullable();
            $table->integer('event_qty')->nullable();
            $table->text('event_price')->nullable();
            $table->text('status')->nullable();
            $table->text('versions')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('fszteam_events');
        Schema::drop('fszteam_generated_dates');
    }

}
