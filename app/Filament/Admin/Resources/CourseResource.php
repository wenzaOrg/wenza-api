<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\Select::make('category')->options([
                'engineering' => 'Engineering & Development',
                'data' => 'Data & Emerging Technologies',
                'design' => 'Design & Creativity',
                'business' => 'Management & Business',
                'security' => 'Security',
            ])->required(),
            Forms\Components\Textarea::make('description')->rows(4),
            Forms\Components\TextInput::make('duration_weeks')->numeric()->required(),
            Forms\Components\TextInput::make('price_ngn')->numeric()->prefix('₦')->required(),
            Forms\Components\TextInput::make('scholarship_price_ngn')->numeric()->prefix('₦'),
            Forms\Components\Toggle::make('is_published'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('price_ngn')->money('NGN'),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
