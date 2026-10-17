<?php

namespace Database\Seeders;

use App\Actions\RecordStockMovement;
use App\Enums\InventoryCategory;
use App\Enums\StockMovementType;
use App\Enums\StockUnit;
use App\Models\InventoryItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Everything the menu is made from, with sample suppliers, costs and an
 * opening count two weeks back. Replace the numbers with real ones.
 */
class InventorySeeder extends Seeder
{
    /**
     * Sample suppliers (fictional), keyed for the item list below.
     *
     * @var array<string, array<string, string|null>>
     */
    private const SUPPLIERS = [
        'coffee' => ['name' => 'Leyte Coffee Traders', 'contact_person' => 'Ramon', 'phone' => '0917 555 0141', 'notes' => 'Delivers Mondays. Minimum order 2 kg.'],
        'dairy' => ['name' => 'Fresh Dairy Distributor', 'contact_person' => 'Liza', 'phone' => '0918 555 0182', 'notes' => 'Order a day ahead.'],
        'syrups' => ['name' => 'Sweet Syrup Supply', 'contact_person' => 'Jun', 'phone' => '0919 555 0123', 'notes' => null],
        'frozen' => ['name' => 'Frozen Fresh Foods', 'contact_person' => 'Carla', 'phone' => '0920 555 0167', 'notes' => 'Siomai, lumpia and fries. Cash on delivery.'],
        'packaging' => ['name' => 'PackRight Packaging', 'contact_person' => 'Mel', 'phone' => '0921 555 0109', 'notes' => 'Cups, lids, straws and stickers.'],
        'market' => ['name' => 'Hindang Public Market', 'contact_person' => null, 'phone' => null, 'notes' => 'Rice, eggs, calamansi and garlic. Pay cash.'],
    ];

    /**
     * sku => [name, category, unit, reorder level, unit cost, opening stock, supplier]
     *
     * @var array<string, array{0: string, 1: InventoryCategory, 2: StockUnit, 3: int, 4: float, 5: int, 6: string}>
     */
    private const ITEMS = [
        'BEAN-ESP' => ['Espresso beans (house blend)', InventoryCategory::CoffeeAndTea, StockUnit::Gram, 1000, 0.55, 5000, 'coffee'],
        'MATCHA' => ['Matcha powder', InventoryCategory::CoffeeAndTea, StockUnit::Gram, 200, 0.80, 600, 'coffee'],

        'MILK-FRESH' => ['Fresh milk', InventoryCategory::Dairy, StockUnit::Milliliter, 5000, 0.085, 24000, 'dairy'],
        'MILK-COND' => ['Condensed milk', InventoryCategory::Dairy, StockUnit::Milliliter, 1000, 0.12, 2400, 'dairy'],
        'CREAM-WHIP' => ['Whipped cream', InventoryCategory::Dairy, StockUnit::Gram, 500, 0.25, 2500, 'dairy'],
        'CHEESE-SAUCE' => ['Cheese sauce', InventoryCategory::Dairy, StockUnit::Gram, 500, 0.22, 2000, 'dairy'],

        'SAUCE-CHOC' => ['Chocolate sauce', InventoryCategory::SyrupsAndMixers, StockUnit::Milliliter, 500, 0.20, 3000, 'syrups'],
        'SAUCE-CARA' => ['Caramel sauce', InventoryCategory::SyrupsAndMixers, StockUnit::Milliliter, 500, 0.18, 1800, 'syrups'],
        'SYRUP-VAN' => ['Vanilla syrup', InventoryCategory::SyrupsAndMixers, StockUnit::Milliliter, 300, 0.25, 1500, 'syrups'],
        'SYRUP-UBE' => ['Ube syrup', InventoryCategory::SyrupsAndMixers, StockUnit::Milliliter, 300, 0.22, 1500, 'syrups'],
        'JAM-STRAW' => ['Strawberry jam', InventoryCategory::SyrupsAndMixers, StockUnit::Milliliter, 500, 0.20, 3000, 'syrups'],
        'SYRUP-MANGO' => ['Mango syrup', InventoryCategory::SyrupsAndMixers, StockUnit::Milliliter, 300, 0.18, 1500, 'syrups'],
        'SYRUP-STRAW' => ['Strawberry syrup', InventoryCategory::SyrupsAndMixers, StockUnit::Milliliter, 300, 0.18, 1500, 'syrups'],
        'SYRUP-APPLE' => ['Green apple syrup', InventoryCategory::SyrupsAndMixers, StockUnit::Milliliter, 300, 0.18, 1500, 'syrups'],
        'SYRUP-BLUE' => ['Blueberry syrup', InventoryCategory::SyrupsAndMixers, StockUnit::Milliliter, 300, 0.18, 1500, 'syrups'],
        'SODA-WATER' => ['Soda water', InventoryCategory::SyrupsAndMixers, StockUnit::Milliliter, 5000, 0.02, 24000, 'syrups'],

        'FRAPPE-BASE' => ['Frappe base powder', InventoryCategory::PowdersAndToppings, StockUnit::Gram, 500, 0.25, 3000, 'syrups'],
        'CRUMBS-COOKIE' => ['Cookie crumbs', InventoryCategory::PowdersAndToppings, StockUnit::Gram, 300, 0.25, 1500, 'syrups'],
        'PWD-CHEESE' => ['Cheese powder', InventoryCategory::PowdersAndToppings, StockUnit::Gram, 200, 0.35, 1000, 'frozen'],
        'PWD-SOURCREAM' => ['Sour cream powder', InventoryCategory::PowdersAndToppings, StockUnit::Gram, 200, 0.35, 1000, 'frozen'],
        'PWD-BBQ' => ['BBQ powder', InventoryCategory::PowdersAndToppings, StockUnit::Gram, 200, 0.35, 1000, 'frozen'],

        'SIOMAI' => ['Siomai (frozen)', InventoryCategory::Food, StockUnit::Piece, 100, 2.50, 400, 'frozen'],
        'LUMPIA' => ['Lumpia (frozen)', InventoryCategory::Food, StockUnit::Piece, 50, 3.50, 200, 'frozen'],
        'FRIES' => ['Potato fries (frozen)', InventoryCategory::Food, StockUnit::Gram, 2000, 0.12, 8000, 'frozen'],
        'NACHO-CHIPS' => ['Nacho chips', InventoryCategory::Food, StockUnit::Gram, 500, 0.22, 2000, 'frozen'],
        'NOODLE-BULDAK' => ['Buldak carbonara noodles', InventoryCategory::Food, StockUnit::Piece, 10, 48.00, 30, 'frozen'],
        'VEGGIE-PACK' => ['Crispy dried veggies (pack)', InventoryCategory::Food, StockUnit::Piece, 10, 18.00, 30, 'frozen'],
        'JALAPENO' => ['Jalapeños', InventoryCategory::Food, StockUnit::Gram, 200, 0.30, 600, 'frozen'],
        'RICE' => ['Rice', InventoryCategory::Food, StockUnit::Gram, 5000, 0.05, 25000, 'market'],
        'EGG' => ['Eggs', InventoryCategory::Food, StockUnit::Piece, 30, 7.50, 120, 'market'],
        'CALAMANSI' => ['Calamansi', InventoryCategory::Food, StockUnit::Piece, 50, 0.80, 200, 'market'],
        'GARLIC' => ['Garlic (minced)', InventoryCategory::Food, StockUnit::Gram, 300, 0.15, 1000, 'market'],
        'OIL-COOK' => ['Cooking oil', InventoryCategory::Food, StockUnit::Milliliter, 2000, 0.08, 8000, 'market'],
        'OIL-CHILI' => ['Chili garlic oil', InventoryCategory::Food, StockUnit::Milliliter, 200, 0.20, 1000, 'market'],

        'CUP-16' => ['Plastic cup 16 oz', InventoryCategory::Packaging, StockUnit::Piece, 100, 2.20, 500, 'packaging'],
        'CUP-22' => ['Plastic cup 22 oz', InventoryCategory::Packaging, StockUnit::Piece, 100, 2.80, 250, 'packaging'],
        'SEAL-FILM' => ['Cup sealing film', InventoryCategory::Packaging, StockUnit::Piece, 200, 0.35, 1000, 'packaging'],
        'LID-DOME' => ['Dome lid', InventoryCategory::Packaging, StockUnit::Piece, 50, 1.20, 300, 'packaging'],
        'STRAW' => ['Straw', InventoryCategory::Packaging, StockUnit::Piece, 200, 0.25, 1000, 'packaging'],
        'STRAW-WIDE' => ['Wide straw (frappe)', InventoryCategory::Packaging, StockUnit::Piece, 100, 0.45, 500, 'packaging'],
        'STICKER' => ['RacNin logo sticker', InventoryCategory::Packaging, StockUnit::Piece, 200, 0.40, 1000, 'packaging'],
        'CARTON-FRIES' => ['Fries carton', InventoryCategory::Packaging, StockUnit::Piece, 50, 1.80, 300, 'packaging'],
        'BOWL' => ['Food bowl (to go)', InventoryCategory::Packaging, StockUnit::Piece, 50, 2.20, 300, 'packaging'],
    ];

    public function run(RecordStockMovement $recordStockMovement): void
    {
        $suppliers = collect(self::SUPPLIERS)->map(fn (array $supplier): Supplier => Supplier::create($supplier));
        $countedAt = today()->subDays(14)->setTime(8, 0);
        $user = User::first();

        foreach (self::ITEMS as $sku => [$name, $category, $unit, $reorderLevel, $unitCost, $openingStock, $supplier]) {
            $item = InventoryItem::create([
                'supplier_id' => $suppliers[$supplier]->id,
                'name' => $name,
                'sku' => $sku,
                'category' => $category,
                'unit' => $unit,
                'reorder_level' => $reorderLevel,
                'unit_cost' => $unitCost,
            ]);

            $recordStockMovement->handle($item, StockMovementType::Adjustment, $openingStock, notes: 'Opening stock', user: $user, occurredAt: $countedAt);
        }
    }
}
