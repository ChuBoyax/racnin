<?php

namespace App\Filament\Resources\InventoryItems\Actions;

use App\Actions\RecordStockCount;
use App\Models\InventoryItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Enter what's physically on the shelf; any difference becomes an adjustment.
 */
class CountStockAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'countStock';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Stock count')
            ->icon(Heroicon::OutlinedClipboardDocumentList)
            ->color('warning')
            ->modalHeading(fn (InventoryItem $record): string => "Count: {$record->name}")
            ->modalDescription(fn (InventoryItem $record): string => 'The system expects '.$record->unit->format($record->stock_on_hand).'. Enter what you actually counted.')
            ->modalSubmitActionLabel('Save count')
            ->modalWidth('md')
            ->fillForm(fn (InventoryItem $record): array => ['counted' => (float) $record->stock_on_hand])
            ->schema([
                TextInput::make('counted')
                    ->label('Counted quantity')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->suffix(fn (InventoryItem $record): string => $record->unit->abbreviation())
                    ->autofocus(),
                Textarea::make('notes')
                    ->rows(2)
                    ->maxLength(200)
                    ->placeholder('Physical count'),
            ])
            ->action(function (array $data, InventoryItem $record, RecordStockCount $recordStockCount): void {
                $movement = $recordStockCount->handle($record, (float) $data['counted'], $data['notes'] ?: null, auth()->user());

                Notification::make()
                    ->success()
                    ->title($movement ? "Adjusted {$record->name} by ".$record->unit->format($movement->quantity) : "{$record->name} matches the count")
                    ->body('On hand: '.$record->unit->format($record->stock_on_hand))
                    ->send();
            });
    }
}
