<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'User & Grade';
    public static function canViewAny(): bool
{
    return auth()->check() && auth()->user()->hasAnyPermission([
        'view_user',
        'view_any_user',
        'create_user',
        'update_user',
        'delete_user'
    ]);
}
    public static function form(Form $form): Form
    {
        $isTeacher = auth()->user()->hasRole('teacher');
        return $form
            ->schema([
                //

                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),


            Forms\Components\Select::make('role')
                ->options([
                    'admin' => 'Admin',
                    'teacher' => 'Teacher',
                    'student' => 'Student',
                ])
                ->required()
                ->hidden($isTeacher)
                ->reactive()
                ->default(function (?User $record) {
                    return $record?->getRoleNames()->first();
                })
                ->afterStateUpdated(function ($set, $state) {
                    $set('grade_id', null);
                    $set('grades', []);
                }),


            Forms\Components\Select::make('grade_id')
                ->relationship('grade', 'name')
                ->required()
                ->visible(fn ($get) => $get('role') === 'student')
                ->default(function (?User $record) {
                    if ($record && $record->hasRole('student')) {
                        return $record->grade_id;
                    }
                    return null;
                }),


            Forms\Components\BelongsToManyMultiSelect::make('grades')
                ->relationship('grades', 'name')
                ->visible(fn ($get) => $get('role') === 'teacher' && !$isTeacher)
                ->default(function (?User $record) {
                    if ($record && $record->hasRole('teacher')) {
                        return $record->grades->pluck('id')->toArray();
                    }
                    return [];
                }),

            Forms\Components\TextInput::make('password')
                ->password()
                ->required(fn (string $context): bool => $context === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('email')
                ->searchable(),
            Tables\Columns\TextColumn::make('roles.name')
                ->label('Role'),
            Tables\Columns\TextColumn::make('grade.name')
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['roles', 'grade']);

        if (auth()->user()->hasRole('teacher')) {
            return $query->whereHas('roles', fn ($q) => $q->where('name', 'student'))
                ->whereIn('grade_id', auth()->user()->grades->pluck('id'))
                ->withoutGlobalScopes([SoftDeletingScope::class]);
        }

        return $query->withoutGlobalScopes([SoftDeletingScope::class]);



    }



    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'restore',
        ];
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
