<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);


        $permissions = [

            'view_my::grade'
        ];


        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }


        $adminRole->givePermissionTo(Permission::all());



        $teacherPermissions = [
            'view_user',
            'view_mark',
            'create_mark',
            'update_mark',
            'delete_mark',
        ];

        // foreach ($teacherPermissions as $permission) {
        //     Permission::firstOrCreate(['name' => $permission]);
        // }


        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $teacherRole->givePermissionTo(
            Permission::whereIn('name', $teacherPermissions)->get()
        );


        $studentRole->givePermissionTo(['view_my::grade']);
    }
}
