<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Finish;
use App\Models\Product;
use App\Models\Room;
use App\Models\TrustBadge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $tables = Category::create([
            'name' => 'Tables',
            'slug' => 'tables',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $diningTables = Category::create([
            'name' => 'Dining Tables',
            'slug' => 'dining-tables',
            'parent_id' => $tables->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $rooms = collect([
            'Lounge',
            'Dining Room',
            'Kitchen',
            'Bedroom',
            'Bathroom',
            'Office',
            'Hallway',
            'Garden',
        ])->mapWithKeys(fn (string $name, int $i) => [
            $name => Room::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'sort_order' => $i + 1,
                'is_active' => true,
            ]),
        ]);

        $this->seedRoundDiningTable([$tables->id, $diningTables->id], [$rooms['Dining Room']->id]);
        $this->seedTvUnit([], [$rooms['Lounge']->id]);
        $this->seedCoffeeTableWithBench([$tables->id], [$rooms['Lounge']->id]);
    }

    private function attachStorefrontDefaults(Product $product): void
    {
        $product->finishes()->sync(Finish::query()->where('is_active', true)->pluck('id')->all());
        $product->trustBadges()->sync(TrustBadge::query()->where('is_active', true)->pluck('id')->all());
    }

    /**
     * @param  array<int, int>  $categoryIds
     * @param  array<int, int>  $roomIds
     */
    private function seedRoundDiningTable(array $categoryIds, array $roomIds): void
    {
        $product = Product::create([
            'name' => 'ALFIE - Rustic Round Dining Table With Hairpin Legs',
            'slug' => 'alfie-rustic-round-dining-table-with-hairpin-legs-made-from-reclaimed-wood-for-indoor-or-outdoor-use',
            'short_description' => 'Made From Reclaimed Wood-For Indoor Or Outdoor Use',
            'description' => 'A handcrafted round dining table built from solid oak. Each table is finished by hand in our workshop.',
            'base_price' => 595.00,
            'is_active' => true,
        ]);
        $product->categories()->sync($categoryIds);
        $product->rooms()->sync($roomIds);
        $this->attachStorefrontDefaults($product);

        $diameter = $product->attributes()->create(['name' => 'Diameter', 'sort_order' => 1]);

        $diameters = collect(['100cm', '120cm', '140cm'])->map(
            fn (string $v, int $i) => $diameter->values()->create(['value' => $v, 'sort_order' => $i]),
        );

        $priceMatrix = [
            '100cm' => 595.00,
            '120cm' => 725.00,
            '140cm' => 825.00,
        ];

        foreach ($diameters as $d) {
            $variant = $product->variants()->create([
                'sku' => 'RND-'.Str::upper(Str::random(6)),
                'price' => $priceMatrix[$d->value],
                'stock_quantity' => 2,
                'is_active' => true,
            ]);
            $variant->attributeValues()->attach([$d->id]);
        }
    }

    /**
     * @param  array<int, int>  $categoryIds
     * @param  array<int, int>  $roomIds
     */
    private function seedTvUnit(array $categoryIds, array $roomIds): void
    {
        $product = Product::create([
            'name' => 'MILTON - Rustic Reclaimed Wood Console Table',
            'slug' => 'milton-rustic-reclaimed-wood-console-table',
            'short_description' => 'Rustic TV Unit, Solid Wood Sideboard, Rustic Bookcase',
            'description' => 'Built to order from reclaimed pine. Choose your length, depth, and finish.',
            'base_price' => 425.00,
            'is_active' => true,
        ]);
        $product->categories()->sync($categoryIds);
        $product->rooms()->sync($roomIds);
        $this->attachStorefrontDefaults($product);

        $length = $product->attributes()->create(['name' => 'Length', 'sort_order' => 1]);
        $depth = $product->attributes()->create(['name' => 'Depth', 'sort_order' => 2]);

        $lengths = ['120cm', '160cm'];
        $depths = ['40cm', '50cm'];

        $lengthValues = collect($lengths)->map(fn (string $v, int $i) => $length->values()->create(['value' => $v, 'sort_order' => $i]));
        $depthValues = collect($depths)->map(fn (string $v, int $i) => $depth->values()->create(['value' => $v, 'sort_order' => $i]));

        foreach ($lengthValues as $li => $l) {
            foreach ($depthValues as $di => $d) {
                $base = 425 + ($li * 80) + ($di * 30);
                $variant = $product->variants()->create([
                    'sku' => 'TV-'.Str::upper(Str::random(6)),
                    'price' => $base,
                    'stock_quantity' => 1,
                    'is_active' => true,
                ]);
                $variant->attributeValues()->attach([$l->id, $d->id]);
            }
        }
    }

    /**
     * @param  array<int, int>  $categoryIds
     * @param  array<int, int>  $roomIds
     */
    private function seedCoffeeTableWithBench(array $categoryIds, array $roomIds): void
    {
        $product = Product::create([
            'name' => 'Chunky Coffee Table',
            'slug' => 'chunky-coffee-table',
            'short_description' => 'Solid wood coffee table with optional matching bench.',
            'description' => 'A chunky, hand-finished coffee table. Pair it with the matching bench for a set.',
            'base_price' => 295.00,
            'is_active' => true,
        ]);
        $product->categories()->sync($categoryIds);
        $product->rooms()->sync($roomIds);
        $this->attachStorefrontDefaults($product);

        $variant = $product->variants()->create([
            'sku' => 'COFFEE-'.Str::upper(Str::random(6)),
            'price' => 295.00,
            'stock_quantity' => 3,
            'is_active' => true,
        ]);
        $variant->attributeValues()->attach([]);
        $product->addons()->create([
            'name' => 'Matching Bench',
            'description' => 'Same wood and finish as your coffee table.',
            'price' => 175.00,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product->addons()->create([
            'name' => 'Felt Pads Set',
            'description' => 'Protective felt pads for the legs.',
            'price' => 8.00,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }
}
