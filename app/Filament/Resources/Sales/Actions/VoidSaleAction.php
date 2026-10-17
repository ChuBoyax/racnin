<?php

namespace App\Filament\Resources\Sales\Actions;

use App\Actions\VoidSale;
use App\Models\Sale;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class VoidSaleAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'void';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Void')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(fn (Sale $record): string => "Void sale {$record->number}?")
            ->modalDescription('Everything this sale took from stock goes back. The sale stays in your history, marked as voided.')
            ->modalSubmitActionLabel('Void sale')
            ->schema([
                Textarea::make('reason')
                    ->rows(2)
                    ->maxLength(255)
                    ->placeholder('Entered by mistake, customer cancelled…'),
            ])
            ->hidden(fn (Sale $record): bool => $record->isVoided())
            ->action(function (array $data, Sale $record, VoidSale $voidSale): void {
                $voidSale->handle($record, $data['reason'] ?: null, auth()->user());

                Notification::make()
                    ->success()
                    ->title("Sale {$record->number} voided")
                    ->body('Its ingredients are back in stock.')
                    ->send();
            });
    }
}
