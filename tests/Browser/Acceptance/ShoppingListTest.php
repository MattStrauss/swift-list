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
        $this->recipe2 = factory(Recipe::class)->create(['name' => 'Test Recipe Two', 'category_id' => 2]);
        $this->shoppingList1 = factory(ShoppingList::class)->create(['name' => 'Test Shopping List One']);
        $this->shoppingList2 = factory(ShoppingList::class)->create(['name' => 'Test Shopping List Two']);
        $this->shoppingList1->recipes()->sync($this->recipe1);
        $this->shoppingList1->items()->sync($this->item1);

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
                    ->pause(300)
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
                    ->assertDontSee('Coffee')
                    ->type('autocomplete_recipe', 'tes')
                    ->keys('[name=autocomplete_recipe]', '{down}', '{enter}')
                    ->pause(300)
                    ->assertSee('Test Recipe One')
                    ->mouseover('.recipes-on-list i.fa-times-circle')
                    ->click('.recipes-on-list .fa-times')
                    ->assertDontSee('Test Recipe One')
                    ->type('autocomplete_recipe', 'tes')
                    ->pause(300)
                    ->keys('[name=autocomplete_recipe]', '', '{down}', '{enter}')
                    ->pause(300)
                    ->assertSee('Test Recipe One')
                    ->mouseover('.items-on-list i.fa-times-circle')
                    ->click('.items-on-list .fa-times')
                    ->pause(500)
                    ->assertDontSee('AAAA')
                    ->type('autocomplete', 'aaa')
                    ->pause(300)
                    ->keys('[name=autocomplete]', '', '{down}', '{enter}')
                    ->pause(300)
                    ->assertSee('AAAA');

            $this->assertDatabaseHas('shopping_lists', ['name' => 'Test List', 'id' => 3, 'user_id' => $this->user->id]);
            $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $this->recipe1->id, 'shopping_list_id' => 1]);
            $this->assertDatabaseMissing('recipe_shopping_list', ['recipe_id' => $this->recipe2->id, 'shopping_list_id' => 1]);
            $this->assertDatabaseHas('item_shopping_list', ['item_id' => $this->item1->id, 'shopping_list_id' => 1]);
            $this->assertDatabaseMissing('item_shopping_list', ['item_id' => $this->itemFavorite->id, 'shopping_list_id' => 1]);
        });
    }

    /** @test */
    public function editShoppingList()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/shopping-lists')
                    ->clickLink('Test Shopping List One')
                    ->assertPathIs('/shopping-lists/1')
                    ->clickLink('Edit List')
                    ->assertPathIs('/shopping-lists/1/edit')
                    ->assertSee('Test Shopping List One')
                    ->assertSee('Apples')
                    ->assertSee('Test Recipe One')
                    ->type('name', 'Test Shopping List Changed')
                    ->type('autocomplete_recipe', 'tes')
                    ->pause(300)
                    ->keys('[name=autocomplete_recipe]', '{down}', '{down}', '{enter}')
                    ->pause(300)
                    ->assertSee('Test Recipe Two')
                    ->mouseover('.recipes-on-list i.fa-times-circle')
                    ->click('.recipes-on-list .fa-times')
                    ->assertDontSee('Test Recipe One')
                    ->type('autocomplete_recipe', 'tes')
                    ->pause(300)
                    ->keys('[name=autocomplete_recipe]', '', '{down}', '{down}', '{enter}')
                    ->pause(300)
                    ->assertSee('Test Recipe Two')
                    ->mouseover('.items-on-list i.fa-times-circle')
                    ->click('.items-on-list .fa-times')
                    ->assertDontSee('Apples')
                    ->type('autocomplete', 'appl')
                    ->pause(300)
                    ->keys('[name=autocomplete]', '', '{down}', '{enter}')
                    ->pause(300)
                    ->assertSee('Apples')
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
                    ->assertDontSee('Coffee')
                    ->clickLink('Favorite Items')
                    ->pause(300)
                    ->assertSee('Coffee');

            $this->assertDatabaseHas('shopping_lists', ['name' => 'Test Shopping List Changed', 'id' => 1, 'user_id' => $this->user->id]);
            $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $this->recipe2->id, 'shopping_list_id' => 1]);
            $this->assertDatabaseMissing('recipe_shopping_list', ['recipe_id' => $this->recipe1->id, 'shopping_list_id' => 1]);
            $this->assertDatabaseHas('item_shopping_list', ['item_id' => $this->item1->id, 'shopping_list_id' => 1]);
            $this->assertDatabaseHas('item_shopping_list', ['item_id' => $this->item2->id, 'shopping_list_id' => 1]);
            $this->assertDatabaseHas('item_shopping_list', ['item_id' => $this->itemFavorite->id, 'shopping_list_id' => 1]);
        });
    }

    /** @test */
    public function showShoppingList()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/shopping-lists')
                    ->clickLink('Test Shopping List One')
                    ->assertPathIs('/shopping-lists/1')
                    ->assertSee('Recipes')
                    ->assertSee('List')
                    ->assertSee('Test Shopping List One')
                    ->assertSee('Fruits')
                    ->assertSee('Apples');
        });
    }

    /** @test */
    public function indexShoppingList()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/shopping-lists')
                    ->assertSee('Test Shopping List One');
        });
    }

    /** @test */
    public function deleteShoppingList()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/shopping-lists')
                    ->assertSee('Test Shopping List One')
                    ->clickLink('Test Shopping List One')
                    ->assertPathIs('/shopping-lists/1')
                    ->click('.fa-trash-alt')
                    ->waitFor('.modal-title')
                    ->click('@modal-confirm-delete-delete-btn')
                    ->waitUntilMissing('@modal-confirm-delete-delete-btn')
                    ->assertPathIs('/shopping-lists')
                    ->assertSee('Shopping List successfully deleted!');

            $this->assertDatabaseMissing('shopping_lists', ['id' => 1, 'user_id' => $this->user->id]);
            $this->assertDatabaseMissing('recipe_shopping_list', ['recipe_id' => $this->recipe1->id, 'shopping_list_id' => 1]);
            $this->assertDatabaseMissing('item_shopping_list', ['item_id' => $this->item1->id, 'shopping_list_id' => 1]);
        });
    }
}
