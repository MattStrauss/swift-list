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

class ShoppingListTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $item1;
    protected $item2;
    protected $itemFavorite;
    protected $shoppingList1;
    protected $shoppingList2;
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
        $this->itemFavorite = factory(Item::class)->create(['name' => 'Coffee', 'aisle_id' => 2, 'favorite' => true]);
        factory(Category::class)->create(['name' => 'Vegetarian']);
        factory(Category::class)->create(['name' => 'Snacks']);
        $this->recipe1 = factory(Recipe::class)->create(['name' => 'Test Recipe One', 'category_id' => 1]);
        $this->recipe1 = factory(Recipe::class)->create(['name' => 'Test Recipe Two', 'category_id' => 2]);
        $this->shoppingList1 = factory(ShoppingList::class)->create(['name' => 'Test Shopping List One']);
        $this->shoppingList2 = factory(ShoppingList::class)->create(['name' => 'Test Shopping List Two']);
    }

    /** @test */
    public function createShoppingList()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/shopping-lists')
                    ->click('.btn-outline-primary')
                    ->assertPathIs('/shopping-lists/create')
                    ->type('name', 'Test List')
                    ->click('.fa-plus-circle')
                    ->waitFor('.modal-body')
                    ->assertSee('Add Item')
                    ->type('@modal-item-name-field', 'Test')
                    ->select('aisle_id', 1)
                    ->click('.dusk-modal-item-add-edit-item-btn')
                    ->pause(300)
                    ->assertSee('Item Added')
                    ->click('.close')
                    ->type('autocomplete', 'AAAA')
                    ->pause(300)
                    ->mouseover('.add-item')
                    ->click("@auto-complete-new-item-span")
                    ->pause(300)
                    ->select('aisle_id', 1)
                    ->click('.dusk-modal-item-add-edit-item-btn')
                    ->pause(300)
                    ->assertSee('Item Added')
                    ->click('.close')
                    ->assertSee('AAAA')
                    ->type('autocomplete', 'app')
                    ->pause(300)
                    ->keys('[name=autocomplete]', '{down}', '{enter}')
                    ->pause(300)
                    ->assertSee('Apples')
                    ->type('autocomplete', 'a')
                    ->pause(300)
                    ->keys('[name=autocomplete]', '{down}', '{down}', '{enter}')
                    ->pause(300)
                    ->assertSee('Bread')
                    ->clickLink('Aisles')
                    ->pause(500)
                    ->assertSee('Fruits')
                    ->assertSee('Bakery')
                    ->clickLink('Fruits')
                    ->pause(300)
                    ->assertSee('Apples')
                    ->clickLink('Bakery')
                    ->pause(300)
                    ->assertSee('Bread')
                    ->clickLink('Aisles')
                    ->assertDontSee('Fruits')
                    ->assertDontSee('Bakery')
                    ->clickLink('Favorite Items')
                    ->pause(300)
                    ->assertSee('Coffee')
                    ->clickLink('Favorite Items')
                    ->pause(300)
                    ->assertDontSee('Coffee');
        });
    }
}
