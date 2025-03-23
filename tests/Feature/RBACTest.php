<?php

namespace Tests\Feature;

use App\Filament\Resources\MyGradeResource;
use App\Filament\Resources\UserResource;
use App\Models\Grade;
use App\Models\User;
use Filament\Pages\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {



    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'teacher']);
    Role::firstOrCreate(['name' => 'student']);

    Permission::firstOrCreate(['name' => 'view_user']);
    Permission::firstOrCreate(['name' => 'view_any_user']);
    Permission::firstOrCreate(['name' => 'create_user']);
    Permission::firstOrCreate(['name' => 'update_user']);
    Permission::firstOrCreate(['name' => 'delete_user']);


    $this->grade = Grade::factory()->create();


    $this->admin = User::factory()->create(['email' => 'admin@test.com'])
        ->assignRole('admin')
        ->givePermissionTo(Permission::all());



    $this->teacher = User::factory()->create(['email' => 'teacher@test.com'])
        ->assignRole('teacher')
        ->refresh();


    $this->teacher->grades()->attach($this->grade);

    $this->student = User::factory()->create([
        'email' => 'student@test.com',
        'grade_id' => $this->grade->id
    ])->assignRole('student');
});

describe('Admin Features', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can view user index', function () {
        livewire(UserResource\Pages\ListUsers::class)
            ->assertSuccessful();
    });

    it('can create users', function () {
        $newUser = User::factory()->make();

        livewire(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => $newUser->name,
                'email' => $newUser->email,
                'role' => 'student',
                'grade_id' => $this->grade->id,
                'password' => 'Password123!',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'email' => $newUser->email,
            'grade_id' => $this->grade->id
        ]);
    });

    it('can edit user roles', function () {
        $newGrade = Grade::factory()->create();

        livewire(UserResource\Pages\EditUser::class, ['record' => $this->student->id])
            ->fillForm([
                'name' => $this->student->name,
                'role' => 'teacher',
                'grades' => [$newGrade->id],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->student->refresh();

        expect($this->student)
            ->hasRole('teacher')->toBeTrue()
            ->grades->pluck('id')->toContain($newGrade->id);
    });


    it('can delete users', function () {

        $student = User::factory()->create(['grade_id' => $this->grade->id])
            ->assignRole('student');

        livewire(UserResource\Pages\EditUser::class, ['record' => $student->id])
            ->callAction('delete', data: ['reason' => 'Test deletion'])
            ->assertHasNoActionErrors();

        $this->assertSoftDeleted($student);

    });
});

// describe('Teacher Features', function () {
//     beforeEach(function () {
//         $this->actingAs($this->teacher);
//     });


//     it('can only view assigned students', function () {

//         $assignedStudent = User::factory()->create()
//             ->assignRole('student');
//         $assignedStudent->grades()->attach($this->grade);

//         $unassignedStudent = User::factory()->create()
//             ->assignRole('student')
//             ->grades()->attach(Grade::factory()->create());

//         livewire(UserResource\Pages\ListUsers::class)
//             ->assertCanSeeTableRecords([$assignedStudent])
//             ->assertCanNotSeeTableRecords([$unassignedStudent]);
//     });

//     it('cannot access teacher role field', function () {
//         livewire(UserResource\Pages\CreateUser::class)
//             ->assertFormFieldDoesNotExist('role');
//     });



//     it('can only assign managed grades', function () {
//         $unmanagedGrade = Grade::factory()->create();

//         livewire(UserResource\Pages\CreateUser::class)
//             ->fillForm([
//                 'name' => 'New Student',
//                 'email' => 'new@test.com',
//                 'grade_id' => $unmanagedGrade->id,
//                 'password' => 'Password123!',
//             ])
//             ->call('create')
//             ->assertHasFormErrors(['grade_id']);
//     });
// });

describe('Student Features', function () {
    beforeEach(function () {
        $this->actingAs($this->student);
    });

    it('cannot access user resource', function () {
        livewire(UserResource\Pages\ListUsers::class)
            ->assertForbidden();
    });


    // it('can view own grades', function () {

    //     $student = $this->student->fresh();


    //     livewire(MyGradeResource\Pages\ManageMyGrades::class)
    //         ->assertSet('grade_id', $student->grade_id)
    //         ->assertSee($this->grade->name);


    //     $student->grades()->attach($this->grade);
    //     livewire(MyGradeResource\Pages\ManageMyGrades::class)
    //         ->assertCanSeeTableRecords($student->grades);
    // });
});

describe('Validation', function () {
    it('requires password on create', function () {
        $this->actingAs($this->admin);

        livewire(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'email' => 'test@test.com',
                'role' => 'student',
                'grade_id' => $this->grade->id,
            ])
            ->call('create')
            ->assertHasFormErrors(['password']);
    });

    it('requires unique emails', function () {
        $this->actingAs($this->admin);

        User::factory()->create(['email' => 'duplicate@test.com']);

        livewire(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'email' => 'duplicate@test.com',
                'role' => 'student',
                'grade_id' => $this->grade->id,
                'password' => 'Password123!',
            ])
            ->call('create')
            ->assertHasFormErrors(['email']);
    });
});
