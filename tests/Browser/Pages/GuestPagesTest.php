<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GuestPagesTest extends DuskTestCase
{

    /** @test */
    public function HomePage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Swift Grocery List')
                    ->assertSee('Login');
        });
    }

    /** @test */
    public function LoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertSee('Swift Grocery List')
                    ->assertSee('E-Mail Address')
                    ->assertSee('Password')
                    ->assertSee('Login')
                    ->assertSee('Forgot Your Password?');
        });
    }

//    /** @test */
//    public function RegisterPage()
//    {
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/register')
//                    ->assertSee('Swift Grocery List')
//                    ->assertSee('E-Mail Address')
//                    ->assertSee('Password')
//                    ->assertSee('Name')
//                    ->assertSee('Confirm Password')
//                    ->assertSee('Register');
//        });
//    }

    /** @test */
    public function GuestCanNotVisitAuthenticatedPages()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/home')
                    ->assertPathIs('/login')
                    ->visit('/shopping-lists')
                    ->assertPathIs('/login')
                    ->visit('/recipes')
                    ->assertPathIs('/login')
                    ->visit('/items')
                    ->assertPathIs('/login')
                    ->visit('/aisles')
                    ->assertPathIs('/login');
        });
    }
}
