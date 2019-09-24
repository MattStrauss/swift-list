<?php

namespace Tests\Browser;

use App\Aisle;
use App\Category;
use App\Item;
use App\Recipe;
use App\ShoppingList;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoggedInPagesTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $item1;
    protected $item2;
    protected $recipe1;
    protected $recipe2;

    protected function setUp() :void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        factory(Aisle::class)->create(['name' => 'Fruits']);
        factory(Aisle::class)->create(['name' => 'Bakery']);
        $this->item1 = factory(Item::class)->create(['name' => 'Apples', 'aisle_id' => 1]);
        $this->item2 = factory(Item::class)->create(['name' => 'Bread', 'aisle_id' => 2]);
        factory(ShoppingList::class)->create(['name' => 'Test Shopping List']);
        factory(Category::class)->create(['name' => 'Beef']);
        factory(Category::class)->create(['name' => 'Italian']);
        $this->recipe1 = factory(Recipe::class)->create(['name' => 'Test Recipe One', 'category_id' => 1]);
        $this->recipe1 = factory(Recipe::class)->create(['name' => 'Test Recipe Two', 'category_id' => 2]);
    }

    /** @test */
    public function homePage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/home')
                    ->assertSee('Shopping Lists')
                    ->assertSee('Recipes')
                    ->assertSee('Items')
                    ->assertSee('Aisle Order');
        });
    }

    /** @test */
    public function ShoppingListsIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/shopping-lists')
                    ->assertSee('Shopping Lists')
                    ->assertSee('Test Shopping List')
                    ->assertSee('New List');
        });
    }

    /** @test */
    public function ShoppingListsCreatePage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/shopping-lists/create')
                    ->assertSee('Shopping Lists')
                    ->assertSee('Recipes')
                    ->assertSee('Recipes On List')
                    ->assertSee('List Name')
                    ->assertSee('Items')
                    ->assertSee('Create')
                    ->assertSee('Back');
        });
    }

    /** @test */
    public function ShoppingListsShowPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/shopping-lists/1')
                    ->assertSee('Shopping Lists')
                    ->assertSee('Test Shopping List')
                    ->assertSee('Recipes')
                    ->assertSee('List')
                    ->assertSee('Delete List')
                    ->assertSee('Edit List')
                    ->assertSee('Print List');
        });
    }

    /** @test */
    public function ShoppingListsEditPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/shopping-lists/1/edit')
                    ->assertSee('Shopping Lists')
                    ->assertSee('Test Shopping List')
                    ->assertSee('Recipes')
                    ->assertSee('Recipes On List')
                    ->assertSee('List Name')
                    ->assertSee('Items')
                    ->assertSee('Update')
                    ->assertSee('Back');
        });
    }

    /** @test */
    public function RecipesIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/recipes')
                    ->assertSee('Recipes')
                    ->assertSee('New Recipe')
                    ->assertSee('Beef')
                    ->assertSee('Italian')
                    ->assertSee('Test Recipe One')
                    ->assertSee('Test Recipe Two');
        });
    }

    /** @test */
    public function RecipesCreatePage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/recipes/create')
                    ->assertSee('Recipes')
                    ->assertSee('Recipe Name')
                    ->assertSee('Category')
                    ->assertSee('Instructions')
                    ->assertSee('Ingredients')
                    ->assertSee('No ingredients')
                    ->assertSee('Back')
                    ->assertSee('Create');
        });
    }

    /** @test */
    public function RecipesShowPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/recipes/1')
                    ->assertSee('Recipes')
                    ->assertSee('Instructions')
                    ->assertSee('Ingredients')
                    ->assertSee('0')
                    ->assertSee('Test Recipe One')
                    ->assertSee('No ingredients')
                    ->assertSee('Delete')
                    ->assertSee('Edit');
        });
    }

    /** @test */
    public function RecipesEditPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/recipes/1/edit')
                    ->assertSee('Recipes')
                    ->assertSee('Recipe Name')
                    ->assertSee('Category')
                    ->assertSee('Instructions')
                    ->assertSee('Ingredients')
                    ->assertSee('Test Recipe One')
                    ->assertSee('Beef')
                    ->assertSee('No ingredients')
                    ->assertSee('Back')
                    ->assertSee('Update');
        });
    }

    /** @test */
    public function ItemsIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->assertSee('Items')
                    ->assertSee('New Item')
                    ->assertSee('Items')
                    ->assertSee('Fruits')
                    ->assertSee('Bakery')
                    ->clickLink('Fruits')
                    ->pause(500)
                    ->assertSee('Apples')
                    ->clickLink('Fruits')
                    ->pause(500)
                    ->assertDontSee('Apples')
                    ->clickLink('Bakery')
                    ->pause(500)
                    ->assertSee('Bread')
                    ->clickLink('Bakery')
                    ->pause(500)
                    ->assertDontSee('Bread');
        });
    }

    /** @test */
    public function AislesIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/aisles')
                    ->assertSee('Aisle Order')
                    ->assertSee('Fruits')
                    ->assertSee('Bakery');
        });
    }
}
