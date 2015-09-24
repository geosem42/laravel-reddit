<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'name' => 'edit-sub',
            'display_name' => 'Edit Subreddit',
            'description' => 'Ability to update a subreddit',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
