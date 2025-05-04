<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ParticipantExporter;
use App\Filament\Imports\ParticipantImporter;
use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\Participant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Участники';
    protected static ?string $modelLabel = 'Участник';
    protected static ?string $pluralModelLabel = 'Участники';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('olympiad_id')
                    ->label('Олимпиада')
                    ->relationship('olympiad', 'name->ru')
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('full_name')
                    ->label('ФИО участника')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('school')
                    ->label('Школа')
                    ->maxLength(255),

                Forms\Components\TextInput::make('code')
                    ->label('Код участника')
                    ->disabled()
                    ->maxLength(255),

                Forms\Components\TextInput::make('total_score')
                    ->label('Общий балл')
                    ->numeric()
                    ->default(0),

                Forms\Components\DateTimePicker::make('finished_time')
                    ->label('Время завершения'),

                Forms\Components\Toggle::make('used')
                    ->label('Использован')
                    ->default(false),

                Forms\Components\Select::make('language')
                    ->label('Язык')
                    ->options([
                        'kk' => 'Казахский',
                        'ru' => 'Русский',
                    ])
                    ->required()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(ParticipantImporter::class),

            ])
            ->columns([
                Tables\Columns\TextColumn::make('olympiad.name')
                    ->label('Олимпиада')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable(),

                Tables\Columns\TextColumn::make('school')
                    ->label('Школа')
                    ->searchable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_score')
                    ->label('Общий балл')
                    ->sortable(),

                Tables\Columns\TextColumn::make('finished_time')
                    ->label('Время завершения')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\IconColumn::make('used')
                    ->label('Использован')
                    ->boolean(),

                Tables\Columns\TextColumn::make('language')
                    ->label('Язык')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('olympiad_id')
                    ->label('Олимпиада')
                    ->relationship('olympiad', 'name->ru'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Редактировать'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Удалить выбранное'),
                ]),
                Tables\Actions\ExportBulkAction::make()
                    ->exporter(ParticipantExporter::class)
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParticipants::route('/'),
            'create' => Pages\CreateParticipant::route('/create'),
            'edit' => Pages\EditParticipant::route('/{record}/edit'),
        ];
    }
}
