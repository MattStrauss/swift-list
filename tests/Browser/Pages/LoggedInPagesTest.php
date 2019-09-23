<?php

namespace Tests\Browser;

use App\Aisle;
use App\Item;
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

    protected function setUp() :void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        factory(Aisle::class)->create(['name' => 'Fruits']);
        factory(Aisle::class)->create(['name' => 'Bakery']);
        $this->item1 = factory(Item::class)->create(['name' => 'Apples', 'aisle_id' => 1]);
        $this->item2 = factory(Item::class)->create(['name' => 'Bread', 'aisle_id' => 2]);
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
        factory(ShoppingList::class)->create(['name' => 'Test Shopping List']);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/shopping-lists')
                    ->assertSee('Shopping Lists')
                    ->assertSee('Test Shopping List')
                    ->assertSee('New List');
        });
    }

    /** @test */
    public function ShoppingListsShowPage()
    {
        factory(ShoppingList::class)->create(['name' => 'Test Shopping List']);

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
        factory(ShoppingList::class)->create(['name' => 'Test Shopping List']);

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
}
