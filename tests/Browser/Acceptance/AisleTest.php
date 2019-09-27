<?php

namespace Tests\Browser;

use App\Aisle;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AisleTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;

    protected function setUp() :void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        factory(Aisle::class)->create(['name' => 'Fruits']);
        factory(Aisle::class)->create(['name' => 'Bakery']);
        factory(Aisle::class)->create(['name' => 'Dairy']);
        factory(Aisle::class)->create(['name' => 'Snacks']);
    }

    /** @test */
    public function visitItemsIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/aisles')
                    ->assertSee('Simply drag and drop the aisles into your desired order.')
                    ->assertSee('Aisle Order')
                    ->assertSee('Fruits')
                    ->assertSee('Dairy')
                    ->assertSee('Bakery')
                    ->assertSee('Snacks');
        });
    }
}
