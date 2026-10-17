<?php

namespace App\Filament\Resources\InventoryItems\Actions;

use App\Actions\RecordStockMovement;
use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Take out stock that was spoiled, spilled or used without a sale.
 */
class RecordWasteAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'recordWaste';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Record waste')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->modalHeading(fn (InventoryItem $record): string => "Record waste: {$record->name}")
            ->modalDescription(fn (InventoryItem $record): string => 'On hand now: '.$record->unit->format($record->stock_on_hand))
            ->modalSubmitActionLabel('Take out of stock')
            ->modalWidth('md')
            ->schema([
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->suffix(fn (InventoryItem $record): string => $record->unit->abbreviation())
                    ->autofocus(),
                Select::make('reason')
                    ->options([
                        'Spoiled or expired' => 'Spoiled or expired',
                        'Spilled or damaged' => 'Spilled or damaged',
                        'Staff meal or sample' => 'Staff meal or sample',
                        'Other' => 'Other',
                    ])
                    ->required()
                    ->native(false),
                Textarea::make('notes')
                    ->rows(2)
                    ->maxLength(200),
            ])
            ->action(function (array $data, InventoryItem $record, RecordStockMovement $recordStockMovement): void {
                $quantity = (float) $data['quantity'];

                $recordStockMovement->handle(
                    $record,
                    StockMovementType::Waste,
                    -$quantity,
                    notes: collect([$data['reason'], $data['notes']])->filter()->join(': '),
                    user: auth()->user(),
                );

                Notification::make()
                    ->success()
                    ->title("Took {$record->unit->format($quantity)} of {$record->name} out of stock")
                    ->body('On hand: '.$record->unit->format($record->stock_on_hand))
                    ->send();
            });
    }
}
