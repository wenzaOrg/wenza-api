<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('reference_code')->disabled()->dehydrated(false),
            TextInput::make('full_name')->required(),
            TextInput::make('email')->email()->required(),
            Select::make('subject')
                ->options([
                    'application_question' => 'Application Question',
                    'scholarship_question' => 'Scholarship Question',
                    'press_partnerships' => 'Press/Partnerships',
                    'other' => 'Other',
                ])
                ->required()
                ->native(false),
            Textarea::make('message')
                ->required()
                ->columnSpanFull(),
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
                TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('subject')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'application_question' => 'blue',
                        'scholarship_question' => 'green',
                        'press_partnerships' => 'purple',
                        'other' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'application_question' => 'Application Question',
                        'scholarship_question' => 'Scholarship Question',
                        'press_partnerships' => 'Press/Partnerships',
                        'other' => 'Other',
                        default => $state,
                    }),
                IconColumn::make('is_read')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('warning'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('subject')
                    ->options([
                        'application_question' => 'Application Question',
                        'scholarship_question' => 'Scholarship Question',
                        'press_partnerships' => 'Press/Partnerships',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('is_read')
                    ->options([
                        '0' => 'Unread',
                        '1' => 'Read',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('mark_as_read')
                        ->label('Mark as Read')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(fn (ContactMessage $record) => $record->update(['is_read' => true]))
                        ->visible(fn (ContactMessage $record) => ! $record->is_read),
                    Action::make('mark_as_replied')
                        ->label('Mark as Replied')
                        ->icon('heroicon-o-check-circle')
                        ->color('info')
                        ->action(fn (ContactMessage $record) => $record->update(['replied_at' => now()]))
                        ->visible(fn (ContactMessage $record) => $record->replied_at === null),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->orderBy('is_read', 'asc'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
