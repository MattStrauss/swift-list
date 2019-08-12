<?php

use App\Aisle;
use App\Category;
use App\Item;
use App\Recipe;
use App\ShoppingList;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $matt = factory(User::class)->create(['email' => 'matt@example.com', 'password' => bcrypt('password'), 'name' => 'Matt']);
        $karen = factory(User::class)->create(['email' => 'karen@example.com', 'password' => bcrypt('password'), 'name' => 'Karen']);

        factory(Category::class, 12)->create();
        factory(Aisle::class, 18)->create();


        for ($i=1; $i < 15; $i++) {

            // # of items
            $numberOfItems = rand(5, 15);

            // matt items
            factory(Item::class, $numberOfItems)->create(['user_id' => $matt->id, 'aisle_id' => Aisle::inRandomOrder()->first()->id]);

            // karen items
            factory(Item::class, $numberOfItems)->create(['user_id' => $karen->id, 'aisle_id' => Aisle::inRandomOrder()->first()->id]);
        }

        for ($i=1; $i < 12; $i++) {

            // # of recipes
            $numberOfRecipes = rand(1, 8);

            // # of items
            $numberOfItems = rand(3, 15);


            // matt recipes
            factory(Recipe::class, $numberOfRecipes)->create(['user_id' => $matt->id, 'category_id' => Category::inRandomOrder()->first()->id])
                ->each(function ($recipe) use ($matt, $numberOfItems) {
                    for ($i = 0; $i < $numberOfItems; $i++) {
                        $recipeItem = factory(Item::class)->create(['user_id' => $matt->id, 'aisle_id' => Aisle::inRandomOrder()->first()->id]);
                        $recipe->items()->save($recipeItem);
                    }
            });


            // karen recipes
            factory(Recipe::class, $numberOfRecipes)->create(['user_id' => $karen->id, 'category_id' => Category::inRandomOrder()->first()->id])
                ->each(function ($recipe) use ($karen, $numberOfItems) {
                    for ($i = 0; $i < $numberOfItems; $i++) {
                        $recipeItem = factory(Item::class)->create(['user_id' => $karen->id, 'aisle_id' => Aisle::inRandomOrder()->first()->id]);
                        $recipe->items()->save($recipeItem);
                    }
                });

        }

            // # of items
            $numberOfItems = rand(10, 35);

            // # of recipes
            $numberOfRecipes = rand(2, 9);

            $now = Carbon::now();

            factory(ShoppingList::class, 12)->create(
                ['user_id' => $matt->id])->each(function ($shoppingList) use (
                $matt,
                $numberOfItems,
                $numberOfRecipes,
                $now
            ) {
                $randomDate = $now->subWeeks(rand(1, 3))->subDays(rand(0, 30));
                $items   = Item::inRandomOrder()->where('user_id', $matt->id)->take($numberOfItems)->get();
                $recipes = Recipe::inRandomOrder()->where('user_id', $matt->id)->take($numberOfRecipes)->get();
                $shoppingList->items()->saveMany($items);
                $shoppingList->recipes()->saveMany($recipes);
                $shoppingList->created_at = $randomDate->format('Y-m-d H:i:s');
                $shoppingList->updated_at = $randomDate->format('Y-m-d H:i:s');
                $shoppingList->name = $randomDate->format('l, M jS'). ' List';
                $shoppingList->save();

            });


            factory(ShoppingList::class, 12)->create(
                ['user_id' => $karen->id])->each(function ($shoppingList) use (
                $karen,
                $numberOfItems,
                $numberOfRecipes,
                $now
            ) {
                $randomDate = $now->subWeeks(rand(1, 3))->subDays(rand(0, 30));
                $items   = Item::inRandomOrder()->where('user_id', $karen->id)->take($numberOfItems)->get();
                $recipes = Recipe::inRandomOrder()->where('user_id', $karen->id)->take($numberOfRecipes)->get();
                $shoppingList->items()->saveMany($items);
                $shoppingList->recipes()->saveMany($recipes);
                $shoppingList->created_at = $randomDate->format('Y-m-d H:i:s');
                $shoppingList->updated_at = $randomDate->format('Y-m-d H:i:s');
                $shoppingList->name = $randomDate->format('l, M jS'). ' List';
                $shoppingList->save();

            });
    }
}
