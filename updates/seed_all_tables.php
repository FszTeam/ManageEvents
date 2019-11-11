<?php namespace FszTeam\ManageEvents\Updates;

use FszTeam\ManageEvents\Models\Calendar;
use October\Rain\Database\Updates\Seeder;

class SeedAllTables extends Seeder
{

    public function run()
    {
        //
        // @todo
        //
        // Add a Welcome post or something
        //

        Calendar::create([
            'name' => 'Uncategorized',
            'slug' => 'uncategorized'
        ]);
    }

}
