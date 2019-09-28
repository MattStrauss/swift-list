<?php

namespace Tests\Browser;

use App\Aisle;
use App\Category;
use App\Item;
use App\Recipe;
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
    public function searchRecipes()
    {
        factory(Recipe::class)->create(['user_id' => $this->user->id, 'name' => 'Noodle Soup', 'category_id' => 1 ]);
        factory(Recipe::class)->create(['user_id' => $this->user->id, 'name' => 'Butter Toast', 'category_id' => 2 ]);
        factory(Recipe::class)->create(['user_id' => $this->user->id, 'name' => 'Grilled Cheese', 'category_id' => 1 ]);
        factory(Recipe::class)->create(['user_id' => $this->user->id, 'name' => 'Ants on a Log', 'category_id' => 2 ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/recipes')
                    ->assertSee('Noodle Soup')
                    ->assertSee('Butter Toast')
                    ->assertSee('Grilled Cheese')
                    ->assertSee('Ants on a Log')
                    ->type('search_recipes', 'noodle')
                    ->pause(300)
                    ->assertSee('Noodle Soup')
                    ->assertDontSee('Butter Toast')
                    ->assertDontSee('Grilled Cheese')
                    ->assertDontSee('Ants on a Log')
                    ->type('search_recipes', 'log')
                    ->pause(300)
                    ->assertDontSee('Noodle Soup')
                    ->assertDontSee('Butter Toast')
                    ->assertDontSee('Grilled Cheese')
                    ->assertSee('Ants on a Log')
                    ->type('search_recipes', 'cheese')
                    ->pause(300)
                    ->assertDontSee('Noodle Soup')
                    ->assertDontSee('Butter Toast')
                    ->assertSee('Grilled Cheese')
                    ->assertDontSee('Ants on a Log')
                    ->type('search_recipes', 'zzzz')
                    ->pause(300)
                    ->assertDontSee('Noodle Soup')
                    ->assertDontSee('Butter Toast')
                    ->assertDontSee('Grilled Cheese')
                    ->assertDontSee('Ants on a Log')
                    ->assertSee('No recipes found!');
        });
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
                    ->click('@recipe-form-submit-button')
                    ->waitFor('.alert-primary')
                    ->assertSee('Recipe Updated!')
                    ->click('.fa-plus-circle')
                    ->waitFor('.modal-body')
                    ->assertSee('Add Item')
                    ->type('@modal-item-name-field', 'Test')
                    ->select('aisle_id', 1)
                    ->click('.dusk-modal-item-add-edit-item-btn')
                    ->waitFor('.alert-primary')
                    ->assertSee('Item Added')
                    ->click('.close')
                    ->type('autocomplete', 'AAAA')
                    ->waitFor('.autocomplete-results')
                    ->mouseover('.add-item')
                    ->click("@auto-complete-new-item-span")
                    ->waitFor('.dusk-modal-item-add-edit-item-btn')
                    ->select('aisle_id', 1)
                    ->click('.dusk-modal-item-add-edit-item-btn')
                    ->waitFor('.alert-primary')
                    ->assertSee('Item Added')
                    ->click('.close')
                    ->assertSee('AAAA')
                    ->type('autocomplete', 'app')
                    ->waitFor('.autocomplete-results')
                    ->keys('[name=autocomplete]', '{down}', '{enter}')
                    ->waitUntilMissing('.autocomplete-results')
                    ->assertSee('Apples')
                    ->type('autocomplete', 'a')
                    ->waitFor('.autocomplete-results')
                    ->keys('[name=autocomplete]', '{down}', '{down}', '{enter}')
                    ->waitUntilMissing('.autocomplete-results')
                    ->assertSee('Bread');

            $this->assertDatabaseHas('recipes', ['name' => 'Test Recipe', 'user_id' => $this->user->id]);
            $this->assertDatabaseHas('item_recipe', ['item_id' => $this->item1->id, 'recipe_id' => 1]);
            $this->assertDatabaseHas('item_recipe', ['item_id' => $this->item2->id, 'recipe_id' => 1]);

        });

    }

    /** @test */
    public function editRecipe()
    {
        $recipe = factory(Recipe::class)->create(['user_id' => $this->user->id]);
        $items = collect([$this->item1]);
        $recipe->items()->sync($items->pluck('id'));

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/recipes')
                    ->click('.list-group-item-action')
                    ->pause(300)
                    ->assertPathIs('/recipes/1')
                    ->click('.btn-outline-secondary')
                    ->assertPathIs('/recipes/1/edit')
                    ->assertSee('Apples')
                    ->type('name', 'Changed to Test Recipe')
                    ->select('category', 2)
                    ->type('instructions', 'Make the food...')
                    ->click('.fa-plus-circle')
                    ->waitFor('.modal-body')
                    ->assertSee('Add Item')
                    ->type('@modal-item-name-field', 'Test')
                    ->select('aisle_id', 1)
                    ->click('.dusk-modal-item-add-edit-item-btn')
                    ->waitFor('.alert-primary')
                    ->assertSee('Item Added')
                    ->click('.close')
                    ->type('autocomplete', 'brea')
                    ->waitFor('.autocomplete-results')
                    ->keys('[name=autocomplete]', '{down}', '{enter}')
                    ->waitUntilMissing('.autocomplete-results')
                    ->assertSee('Bread')
                    ->mouseover('.items-on-list i.fa-times-circle')
                    ->click('.items-on-list .fa-times')
                    ->assertDontSee('Apples')
                    ->click('@recipe-form-submit-button')
                    ->waitFor('.alert-primary')
                    ->assertSee('Recipe Updated');

            $this->assertDatabaseHas('recipes', ['name' => 'Changed to Test Recipe', 'user_id' => $this->user->id]);
            $this->assertDatabaseHas('item_recipe', ['item_id' => $this->item2->id, 'recipe_id' => 1]);
            $this->assertDatabaseMissing('item_recipe', ['item_id' => $this->item1->id, 'recipe_id' => 1]);

        });
    }

    /** @test */
    public function deleteRecipe()
    {
        $recipe = factory(Recipe::class)->create(['user_id' => $this->user->id]);
        $items = collect([$this->item1]);
        $recipe->items()->sync($items->pluck('id'));

        $this->browse(function (Browser $browser) use ($recipe) {
            $browser->loginAs($this->user)
                    ->visit('/recipes')
                    ->click('.list-group-item-action')
                    ->pause(300)
                    ->assertPathIs('/recipes/1')
                    ->click('.btn-outline-danger')
                    ->waitFor('.modal-body')
                    ->assertSee('Confirm Delete')
                    ->click('@modal-confirm-delete-delete-btn')
                    ->waitFor('.alert-primary')
                    ->assertSee('Recipe successfully deleted!')
                    ->assertPathIs('/recipes');

            $this->assertDatabaseMissing('recipes', ['id' => $recipe->id, 'user_id' => $this->user->id]);
            $this->assertDatabaseMissing('item_recipe', ['item_id' => $this->item1->id, 'recipe_id' => $recipe->id]);

        });
    }
}
