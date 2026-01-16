<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(["name"=> "admin"]);
        $admin=User::create(["name"=> "Bharat",
        "email"=>"bharat1312pawar@gmail.com",
        "password"=>Hash::make('Bharat@1312')]);
        $admin->assignRole($role);
        // \App\Models\User::factory(10)->create();
    }
}
