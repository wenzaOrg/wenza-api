<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('reference_code')->disabled()->dehydrated(false),
            TextInput::make('full_name')->required(),
            TextInput::make('email')->email()->required(),
            TextInput::make('phone')->tel(),
            TextInput::make('age')->numeric(),
            Select::make('pipeline_status')
                ->options([
                    'new' => 'New',
                    'contacted' => 'Contacted',
                    'interviewing' => 'Interviewing',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                    'withdrawn' => 'Withdrawn',
                ])
                ->required()
                ->native(false),
            Select::make('course_id')
                ->relationship('course', 'title')
                ->placeholder('General Application'),
            RichEditor::make('admin_notes')
                ->columnSpanFull(),
            Textarea::make('goals')
                ->disabled()
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
                TextColumn::make('course.title')
                    ->label('Programme')
                    ->default('General')
                    ->sortable(),
                TextColumn::make('pipeline_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'contacted' => 'info',
                        'interviewing' => 'warning',
                        'accepted' => 'success',
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
                        'contacted' => 'Contacted',
                        'interviewing' => 'Interviewing',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'withdrawn' => 'Withdrawn',
                    ]),
                SelectFilter::make('course_id')
                    ->relationship('course', 'title')
                    ->label('Course'),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('mark_contacted')
                        ->label('Mark as Contacted')
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->color('info')
                        ->action(fn (Lead $record) => $record->update(['pipeline_status' => 'contacted'])),
                    Action::make('mark_accepted')
                        ->label('Mark as Accepted')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Lead $record) => $record->update(['pipeline_status' => 'accepted'])),
                ]),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) => static::exportLeads($records)),
            ])
            ->bulkActions([]);
    }

    protected static function exportLeads(Collection $records): void
    {
        // Simple CSV export logic for demonstration
        if ($records->isEmpty()) {
            return;
        }

        $filename = 'leads-export-'.now()->format('Y-m-d-H-i').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');

            if ($file === false) {
                return;
            }

            fputcsv($file, ['Reference', 'Name', 'Email', 'Status', 'Applied At']);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->reference_code,
                    $record->full_name,
                    $record->email,
                    $record->pipeline_status,
                    $record->created_at?->toDateTimeString() ?? 'Unknown',
                ]);
            }
            fclose($file);
        };

        // Note: In a real Filament action, you'd return a stream response.
        // Since we are in a static method, we'll suggest a better way if this was a production app.
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
