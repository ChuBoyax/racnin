<?php

namespace App\Filament\Resources\InventoryItems\Actions;

use App\Actions\RecordStockMovement;
use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Receive a delivery: adds stock and, when a cost is given, re-averages the
 * item's unit cost.
 */
class StockInAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'stockIn';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Stock in')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('success')
            ->modalHeading(fn (InventoryItem $record): string => "Stock in: {$record->name}")
            ->modalDescription(fn (InventoryItem $record): string => 'On hand now: '.$record->unit->format($record->stock_on_hand))
            ->modalSubmitActionLabel('Add to stock')
            ->modalWidth('md')
            ->schema([
                TextInput::make('quantity')
                    ->label('Quantity received')
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->suffix(fn (InventoryItem $record): string => $record->unit->abbreviation())
                    ->autofocus(),
                TextInput::make('total_cost')
                    ->label('Total paid')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('₱')
                    ->helperText('For the whole delivery. Leave blank to keep the current unit cost.'),
                TextInput::make('reference')
                    ->label('Receipt or invoice no.')
                    ->maxLength(64),
                Textarea::make('notes')
                    ->rows(2)
                    ->maxLength(255),
            ])
            ->action(function (array $data, InventoryItem $record, RecordStockMovement $recordStockMovement): void {
                $quantity = (float) $data['quantity'];

                $recordStockMovement->handle(
                    $record,
                    StockMovementType::Purchase,
                    $quantity,
                    unitCost: filled($data['total_cost']) ? (float) $data['total_cost'] / $quantity : null,
                    reference: $data['reference'],
                    notes: $data['notes'],
                    user: auth()->user(),
                );

                Notification::make()
                    ->success()
                    ->title("Added {$record->unit->format($quantity)} of {$record->name}")
                    ->body('On hand: '.$record->unit->format($record->stock_on_hand))
                    ->send();
            });
    }
}
