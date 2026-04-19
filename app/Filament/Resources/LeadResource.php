<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('reference')->disabled(),
            TextInput::make('full_name')->disabled(),
            TextInput::make('email')->disabled(),
            TextInput::make('phone')->disabled(),
            TextInput::make('referral_source')->disabled(),
            Select::make('status')
                ->options([
                    'new' => 'New',
                    'contacted' => 'Contacted',
                    'enrolled' => 'Enrolled',
                    'lost' => 'Lost',
                ])
                ->required(),
            Textarea::make('motivation')->disabled()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->searchable(),
                TextColumn::make('full_name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('course.title')->sortable()->label('Course'),
                TextColumn::make('referral_source')->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'new' => 'New',
                    'contacted' => 'Contacted',
                    'enrolled' => 'Enrolled',
                    'lost' => 'Lost',
                ]),
                SelectFilter::make('course_id')->relationship('course', 'title')->label('Course'),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
