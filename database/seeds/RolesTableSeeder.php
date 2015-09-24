<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Users with admin privileges',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('roles')->insert([
            'name' => 'mod',
            'display_name' => 'Moderator',
            'description' => 'Users with mod privileges',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('roles')->insert([
            'name' => 'user',
            'display_name' => 'User',
            'description' => 'Users with user privileges',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
