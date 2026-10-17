<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Actions\RecordSale;
use App\Enums\SaleChannel;
use App\Filament\Resources\Sales\SaleResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected static ?string $title = 'Record a sale';

    /**
     * Sales go through RecordSale so their recipes come out of stock.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return app(RecordSale::class)->handle(
            $data['lines'],
            channel: $data['channel'] instanceof SaleChannel ? $data['channel'] : SaleChannel::from($data['channel']),
            reference: $data['reference'] ?? null,
            customerName: $data['customer_name'] ?? null,
            notes: $data['notes'] ?? null,
            user: auth()->user(),
            soldAt: Carbon::parse($data['sold_at']),
        );
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->label('Record sale');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->label('Record & start another');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return "Sale {$this->record->number} recorded";
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
