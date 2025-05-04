<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OlympiadResource\Pages;
use App\Filament\Resources\OlympiadResource\RelationManagers;
use App\Models\Olympiad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OlympiadResource extends Resource
{
    protected static ?string $model = Olympiad::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Детали теста')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Общая информация')
                            ->schema([
                                // Используем сетку из 2 колонок для ввода названия
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name.kk')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Название на казахском'),

                                        Forms\Components\TextInput::make('name.ru')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Название на русском'),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->label('Тип теста')
                                            ->options([
                                                'online' => 'Онлайн',
                                                'offline' => 'Оффлайн',
                                            ])
                                            ->required(),
                                        Forms\Components\Toggle::make('showResult')
                                            ->label('Показывать результаты')
                                            ->default(false),
                                ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('started_at')
                                            ->label('Начало'),

                                        Forms\Components\DateTimePicker::make('finished_at')
                                            ->label('Окончание'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Вопросы')
                            ->schema([
                                Forms\Components\HasManyRepeater::make('questions')
                                    ->label('Список вопросов')
                                    ->schema([
                                        Forms\Components\Tabs::make('Вкладки вопроса')
                                            ->tabs([
                                                Forms\Components\Tabs\Tab::make('Вопрос')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\RichEditor::make('question_text.kk')
                                                                    ->label('Вопрос (каз.)')
                                                                    ->required(),

                                                                Forms\Components\RichEditor::make('question_text.ru')
                                                                    ->label('Вопрос (рус.)')
                                                                    ->required(),
                                                            ]),
                                                    ]),

                                                Forms\Components\Tabs\Tab::make('Обязательные варианты')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\RichEditor::make('option_a.kk')
                                                                    ->label('Вариант A (каз.)')
                                                                    ->required(),
                                                                Forms\Components\RichEditor::make('option_a.ru')
                                                                    ->label('Вариант A (рус.)')
                                                                    ->required(),
                                                                Forms\Components\RichEditor::make('option_b.kk')
                                                                    ->label('Вариант B (каз.)')
                                                                    ->required(),
                                                                Forms\Components\RichEditor::make('option_b.ru')
                                                                    ->label('Вариант B (рус.)')
                                                                    ->required(),
                                                            ]),
                                                    ]),

                                                Forms\Components\Tabs\Tab::make('Дополнительные варианты')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\RichEditor::make('option_c.kk')
                                                                    ->label('Вариант C (каз.)')
                                                                    ->nullable(),
                                                                Forms\Components\RichEditor::make('option_c.ru')
                                                                    ->label('Вариант C (рус.)')
                                                                    ->nullable(),
                                                                Forms\Components\RichEditor::make('option_d.kk')
                                                                    ->label('Вариант D (каз.)')
                                                                    ->nullable(),
                                                                Forms\Components\RichEditor::make('option_d.ru')
                                                                    ->label('Вариант D (рус.)')
                                                                    ->nullable(),
                                                                Forms\Components\RichEditor::make('option_e.kk')
                                                                    ->label('Вариант E (каз.)')
                                                                    ->nullable(),
                                                                Forms\Components\RichEditor::make('option_e.ru')
                                                                    ->label('Вариант E (рус.)')
                                                                    ->nullable(),
                                                                Forms\Components\RichEditor::make('option_f.kk')
                                                                    ->label('Вариант F (каз.)')
                                                                    ->nullable(),
                                                                Forms\Components\RichEditor::make('option_f.ru')
                                                                    ->label('Вариант F (рус.)')
                                                                    ->nullable(),
                                                                Forms\Components\RichEditor::make('option_g.kk')
                                                                    ->label('Вариант G (каз.)')
                                                                    ->nullable(),
                                                                Forms\Components\RichEditor::make('option_g.ru')
                                                                    ->label('Вариант G (рус.)')
                                                                    ->nullable(),
                                                            ]),
                                                    ]),

                                                Forms\Components\Tabs\Tab::make('Правильный ответ')
                                                    ->schema([
                                                        Forms\Components\Select::make('correct_option')
                                                            ->label('Выберите правильный вариант')
                                                            ->options([
                                                                'A' => 'Вариант A',
                                                                'B' => 'Вариант B',
                                                                'C' => 'Вариант C',
                                                                'D' => 'Вариант D',
                                                                'E' => 'Вариант E',
                                                                'F' => 'Вариант F',
                                                                'G' => 'Вариант G',
                                                            ])
                                                            ->required(),
                                                    ]),
                                            ])
                                    ])
                                    ->defaultItems(1) // по умолчанию один вопрос
                            ])
                    ])
                    ->columnSpanFull()
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finished_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListOlympiads::route('/'),
            'create' => Pages\CreateOlympiad::route('/create'),
            'edit' => Pages\EditOlympiad::route('/{record}/edit'),
        ];
    }
}
