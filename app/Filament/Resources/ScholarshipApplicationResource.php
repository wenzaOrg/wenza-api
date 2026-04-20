<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScholarshipApplicationResource\Pages;
use App\Models\ScholarshipApplication;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScholarshipApplicationResource extends Resource
{
    protected static ?string $model = ScholarshipApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Applications';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('reference_code')->disabled()->dehydrated(false),
            TextInput::make('first_name')->disabled()->dehydrated(false),
            TextInput::make('last_name')->disabled()->dehydrated(false),
            TextInput::make('email')->disabled()->dehydrated(false),
            TextInput::make('phone')->disabled()->dehydrated(false),
            TextInput::make('gender')->disabled()->dehydrated(false),
            TextInput::make('country')->disabled()->dehydrated(false),
            TextInput::make('state_or_city')->disabled()->dehydrated(false),
            TextInput::make('current_status')->disabled()->dehydrated(false),
            TextInput::make('education_level')->disabled()->dehydrated(false),
            TextInput::make('learning_mode')->disabled()->dehydrated(false),
            TextInput::make('prior_tech_experience')->disabled()->dehydrated(false),

            Select::make('pipeline_status')
                ->options([
                    'new' => 'New',
                    'reviewing' => 'Reviewing',
                    'shortlisted' => 'Shortlisted',
                    'accepted' => 'Accepted',
                    'waitlisted' => 'Waitlisted',
                    'rejected' => 'Rejected',
                    'withdrawn' => 'Withdrawn',
                ])
                ->required()
                ->native(false),

            RichEditor::make('admin_notes')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_code')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('applicant_name')
                    ->label('Applicant')
                    ->getStateUsing(fn (ScholarshipApplication $record) => "{$record->first_name} {$record->last_name}")
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('course.title')
                    ->label('Programme')
                    ->sortable(),
                TextColumn::make('cohort.name')
                    ->label('Intake')
                    ->sortable(),
                TextColumn::make('pipeline_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'reviewing' => 'info',
                        'shortlisted' => 'warning',
                        'accepted' => 'success',
                        'waitlisted' => 'info',
                        'rejected' => 'danger',
                        'withdrawn' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('pipeline_status')
                    ->options([
                        'new' => 'New',
                        'reviewing' => 'Reviewing',
                        'shortlisted' => 'Shortlisted',
                        'accepted' => 'Accepted',
                        'waitlisted' => 'Waitlisted',
                        'rejected' => 'Rejected',
                        'withdrawn' => 'Withdrawn',
                    ]),
                SelectFilter::make('course_id')
                    ->relationship('course', 'title')
                    ->label('Programme'),
                SelectFilter::make('cohort_id')
                    ->relationship('cohort', 'name')
                    ->label('Intake'),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('mark_reviewing')
                        ->label('Mark as Reviewing')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('info')
                        ->action(fn (ScholarshipApplication $record) => $record->update(['pipeline_status' => 'reviewing'])),
                    Action::make('mark_shortlisted')
                        ->label('Mark as Shortlisted')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(fn (ScholarshipApplication $record) => $record->update(['pipeline_status' => 'shortlisted'])),
                    Action::make('mark_accepted')
                        ->label('Mark as Accepted')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (ScholarshipApplication $record) => $record->update(['pipeline_status' => 'accepted'])),
                    Action::make('mark_rejected')
                        ->label('Mark as Rejected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (ScholarshipApplication $record) => $record->update(['pipeline_status' => 'rejected'])),
                ]),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScholarshipApplications::route('/'),
            'edit' => Pages\EditScholarshipApplication::route('/{record}/edit'),
        ];
    }
}
