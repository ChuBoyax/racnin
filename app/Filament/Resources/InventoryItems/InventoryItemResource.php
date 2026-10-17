<?php

namespace App\Filament\Resources\InventoryItems;

use App\Filament\Resources\InventoryItems\Pages\CreateInventoryItem;
use App\Filament\Resources\InventoryItems\Pages\EditInventoryItem;
use App\Filament\Resources\InventoryItems\Pages\ListInventoryItems;
use App\Filament\Resources\InventoryItems\RelationManagers\StockMovementsRelationManager;
use App\Filament\Resources\InventoryItems\Schemas\InventoryItemForm;
use App\Filament\Resources\InventoryItems\Tables\InventoryItemsTable;
use App\Models\InventoryItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'Stock items';

    protected static ?string $modelLabel = 'stock item';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return InventoryItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoryItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StockMovementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryItems::route('/'),
            'create' => CreateInventoryItem::route('/create'),
            'edit' => EditInventoryItem::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku'];
    }

    public static function getNavigationBadge(): ?string
    {
        $lowStock = InventoryItem::active()->lowStock()->count();

        return $lowStock > 0 ? (string) $lowStock : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return InventoryItem::active()->where('stock_on_hand', '<=', 0)->exists() ? 'danger' : 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Low on stock';
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
