<?php

namespace Tests\Browser;

use App\Aisle;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ItemsTest extends DuskTestCase
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

    /** @test **/
    public function visitItemsIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->assertSee('To add or remove items to/from your favorites list,');
        });
    }

    /** @test **/
    public function addNewItem()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->click('.dusk-add-new-item')
                    ->waitFor('.modal-body')
                    ->assertSee('Add Item')
                    ->type('name', 'Test')
                    ->select('aisle_id', 1)
                    ->click('.dusk-modal-add-item-btn')
                    ->pause(300)
                    ->assertSee('Item Added');

            $this->assertDatabaseHas('items', ['name' => 'Test']);

        });
    }

}
