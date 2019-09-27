<?php

namespace Tests\Browser;

use App\Aisle;
use App\Item;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ItemTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;

    protected function setUp() :void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        factory(Aisle::class)->create(['name' => 'Fruits']);
        factory(Aisle::class)->create(['name' => 'Bakery']);
    }

    /** @test */
    public function visitItemsIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->assertSee('To add or remove items to/from your favorites list,');
        });
    }

    /** @test */
    public function addNewItem()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->click('.dusk-item-index-add-new-item-btn')
                    ->waitFor('.modal-body')
                    ->assertSee('Add Item')
                    ->type('name', 'Test')
                    ->select('aisle_id', 1)
                    ->click('.dusk-modal-item-add-edit-item-btn')
                    ->pause(300)
                    ->assertSee('Item Added');

            $this->assertDatabaseHas('items', ['name' => 'Test']);
        });
    }

    /** @test */
    public function editItem()
    {
        $item = factory(Item::class)->create();

        $this->browse(function (Browser $browser) use ($item) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->clickLink('Fruits')
                    ->pause(300)
                    ->click('.fa-edit')
                    ->waitFor('.modal-body')
                    ->assertSee('Edit Item')
                    ->type('name', 'Tester')
                    ->click('.dusk-modal-item-add-edit-item-btn')
                    ->pause(300)
                    ->assertSee('Item Edited');

            $this->assertDatabaseHas('items', ['name' => 'Tester', 'id' => $item->id]);
        });
    }

    /** @test */
    public function deleteItem()
    {
        $item = factory(Item::class)->create(['name' => 'Pears']);

        $this->browse(function (Browser $browser) use ($item) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->clickLink('Fruits')
                    ->pause(500)
                    ->assertSee('Pears')
                    ->click('.fa-trash-alt')
                    ->pause(200)
                    ->assertSee('Confirm Delete')
                    ->click('.btn-outline-danger')
                    ->pause(400)
                    ->assertDontSee('Pears');

            $this->assertDatabaseMissing('items', ['name' => 'Pears', 'id' => $item->id]);
        });
    }

}
