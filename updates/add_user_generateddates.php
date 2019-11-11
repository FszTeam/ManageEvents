<?php namespace FszTeam\ManageEvents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use DB;

class AddUserGennerateddates extends Migration
{

    public function up()
    {
        DB::statement('ALTER TABLE fszteam_generated_dates ADD user_id INT');
    }

    public function down()
    {

    }

}
