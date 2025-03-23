<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarkResource\Pages;
use App\Filament\Resources\MarkResource\RelationManagers;
use App\Models\Mark;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;

class MarkResource extends Resource
{
    protected static ?string $model = Mark::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Subject & Mark';
    public static function canViewAny(): bool
{
    return auth()->user()->hasAnyPermission([
        'view_mark',
        'view_any_mark',
        'create_mark',
        'update_mark',
        'delete_mark'
    ]);
}
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('student_id')
                ->label('Student')
                ->options(function () {
                    if (auth()->user()->hasRole('teacher')) {
                        return User::whereHas('grade', function ($query) {
                            $query->whereIn('id', auth()->user()->grades->pluck('id'));
                        })->pluck('name', 'id');
                    }
                    return User::whereHas('roles', fn ($q) => $q->where('name', 'student'))
                        ->pluck('name', 'id');
                })
                ->required()
                ->searchable(),

            Forms\Components\Select::make('subject_id')
                ->relationship('subject', 'name')
                ->required(),

            Forms\Components\TextInput::make('mark')
                ->numeric()
                ->required()
                ->minValue(0)
                ->maxValue(100),

            Forms\Components\Hidden::make('teacher_id')
                ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('student.name')
                ->sortable(),
            Tables\Columns\TextColumn::make('subject.name')
                ->sortable(),
            Tables\Columns\TextColumn::make('mark')
                ->sortable(),
            Tables\Columns\TextColumn::make('teacher.name')
                ->sortable(),
            ])
            ->filters([
                //
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
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('teacher')) {
            return $query->where('teacher_id', auth()->id());
        }

        return $query;
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
            'index' => Pages\ListMarks::route('/'),
            'create' => Pages\CreateMark::route('/create'),
            'edit' => Pages\EditMark::route('/{record}/edit'),
        ];
    }
}
