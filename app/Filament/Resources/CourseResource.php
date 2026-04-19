<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Course Details')->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Select::make('category')
                    ->options([
                        'engineering' => 'Engineering & Development',
                        'data' => 'Data & Emerging Technologies',
                        'design' => 'Design & Creativity',
                        'business' => 'Management & Business',
                        'security' => 'Security',
                    ])
                    ->required(),

                Textarea::make('description')
                    ->rows(4)
                    ->required(),
            ])->columns(2),

            Section::make('Pricing & Duration')->schema([
                TextInput::make('duration_weeks')
                    ->numeric()
                    ->required()
                    ->suffix('weeks'),

                Select::make('format')
                    ->options([
                        'cohort' => 'Cohort-based',
                        'self_paced' => 'Self-paced',
                    ])
                    ->required(),

                TextInput::make('price_ngn')
                    ->numeric()
                    ->prefix('₦')
                    ->required(),

                TextInput::make('price_usd')
                    ->numeric()
                    ->prefix('$'),

                TextInput::make('scholarship_price_ngn')
                    ->numeric()
                    ->prefix('₦')
                    ->helperText('Leave blank if no scholarship pricing'),

                TextInput::make('thumbnail_url')
                    ->url()
                    ->maxLength(500),

                Toggle::make('is_published')
                    ->label('Published')
                    ->helperText('Only published courses appear in the public catalogue'),
                Toggle::make('is_featured')
                    ->label('Featured')
                    ->helperText('Featured courses show on the marketing homepage (max 4).'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category')->badge(),
                TextColumn::make('duration_weeks')->suffix(' wks')->sortable(),
                TextColumn::make('price_ngn')
                    ->prefix('₦')
                    ->numeric(thousandsSeparator: ',')
                    ->sortable(),
                IconColumn::make('is_published')->boolean()->label('Published'),
                IconColumn::make('is_featured')->boolean()->label('Featured'),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')->options([
                    'engineering' => 'Engineering & Development',
                    'data' => 'Data & Emerging Technologies',
                    'design' => 'Design & Creativity',
                    'business' => 'Management & Business',
                    'security' => 'Security',
                ]),
                SelectFilter::make('is_published')->options([
                    '1' => 'Published',
                    '0' => 'Draft',
                ])->label('Status'),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
