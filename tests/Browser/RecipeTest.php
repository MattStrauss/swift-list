<?php

namespace Tests\Browser;

use App\Aisle;
use App\Category;
use App\Item;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RecipeTest extends DuskTestCase
{

    use DatabaseMigrations;

    protected $user;
    protected $item1;
    protected $item2;

    protected function setUp() :void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        factory(Aisle::class)->create(['name' => 'Fruits']);
        factory(Aisle::class)->create(['name' => 'Bakery']);
        $this->item1 = factory(Item::class)->create(['name' => 'Apples', 'aisle_id' => 1]);
        $this->item2 = factory(Item::class)->create(['name' => 'Bread', 'aisle_id' => 2]);
        factory(Category::class)->create(['name' => 'Vegetarian']);
        factory(Category::class)->create(['name' => 'Snacks']);
    }

    /** @test */
    public function createRecipe()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/recipes')
                    ->click('.btn-outline-primary')
                    ->assertPathIs('/recipes/create')
                    ->type('name', 'Test Recipe')
                    ->select('category', 1)
                    ->type('instructions', 'Make the food...')
                    ->click('.fa-plus-circle')
                    ->waitFor('.modal-body')
                    ->assertSee('Add Item')
                    ->type('@modal-item-name-field', 'Test')
                    ->select('aisle_id', 1)
                    ->click('.dusk-modal-item-add-edit-item-btn')
                    ->pause(300)
                    ->assertSee('Item Added')
                    ->click('.close')
                    ->type('autocomplete', 'app')
                    ->waitFor('.autocomplete-results')
                    ->keys('[name=autocomplete]', '{down}', '{enter}')
                    ->pause(200)
                    ->assertSee('Apples')
                    ->type('autocomplete', 'a')
                    ->waitFor('.autocomplete-results')
                    ->keys('[name=autocomplete]', '{down}', '{down}', '{enter}')
                    ->pause(200)
                    ->assertSee('Bread')
                    ->click('.btn-outline-primary')
                    ->pause(200)
                    ->assertSee('Recipe Updated');

            $this->assertDatabaseHas('recipes', ['name' => 'Test Recipe', 'user_id' => $this->user->id]);
            $this->assertDatabaseHas('item_recipe', ['item_id' => $this->item1->id, 'recipe_id' => 1]);
            $this->assertDatabaseHas('item_recipe', ['item_id' => $this->item2->id, 'recipe_id' => 1]);

        });
    }

}
