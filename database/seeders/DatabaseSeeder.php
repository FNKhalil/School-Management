<?php

namespace Database\Seeders;
use App\Models\Grade;
use App\Models\Mark;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $this->call(ShieldSeeder::class);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);


        $admin = User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@sonextech.com',
        ]);
        $admin->assignRole('admin');


        $grades = Grade::factory()->count(5)->create();


        User::factory()->teacher()
            ->count(3)
            ->create()
            ->each(function ($teacher) use ($grades) {
                $teacher->assignRole('teacher');
                $teacher->grades()->attach(
                    $grades->random(2)->pluck('id')->toArray()
                );
            });


        User::factory()->student()
            ->count(10)
            ->create()
            ->each(function ($student) use ($grades) {
                $student->assignRole('student');
                $student->update([
                    'grade_id' => $grades->random()->id
                ]);
            });


        Subject::factory()->count(6)->create();


        Mark::factory()->count(50)->create();


        $permissions = [

            'view_my::grade'
        ];


        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }


        $adminRole->givePermissionTo(Permission::all());

        $studentRole->givePermissionTo(['view_my::grade']);

        $teacherPermissions = [
            'view_user',
            'view_mark',
            'create_mark',
            'update_mark',
            'delete_mark',
        ];

        foreach ($teacherPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }


        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $teacherRole->givePermissionTo(
            Permission::whereIn('name', $teacherPermissions)->get()
        );


    }
}
