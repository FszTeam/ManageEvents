<?php namespace FszTeam\ManageEvents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use DB;

class UpdateGeneratedDateFieldname extends Migration
{

    public function up()
    {
        if (Schema::hasColumn('fszteam_generated_dates', 'date')) {
            Schema::table('fszteam_generated_dates', function ($table) {
                $table->renameColumn('date', 'event_date');
            });
        }
    }

    public function down()
    {

    }

}
