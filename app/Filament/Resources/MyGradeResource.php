<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyGradeResource\Pages;
use App\Filament\Resources\MyGradeResource\RelationManagers;
// use App\Models\Mark;
use App\Models\MyGrade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MyGradeResource extends Resource
{

    protected static ?string $model = MyGrade::class;
    protected static ?string $name = 'my-grade';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Student';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('subject.name')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('mark')
                ->sortable()
                ->color(fn (MyGrade $record) => $record->mark >= 50 ? 'success' : 'danger'),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('student_id', auth()->id())
            ->with(['subject', 'teacher']);
    }

    public static function canViewAny(): bool
    {

        return auth()->check() && auth()->user()->hasRole('student');
    }

    public static function getPermissionPrefixes(): array
    {
        return ['view'];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMyGrades::route('/'),
        ];
    }
}
