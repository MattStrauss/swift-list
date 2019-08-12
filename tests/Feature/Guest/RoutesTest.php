<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoutesTest extends TestCase
{
    /** @test  */
    public function guestCanAccessSiteRootPage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test  */
    public function guestCanAccessLoginPage()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }


    /** @test  */
    public function guestCanAccessRegisterPage()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /** @test  */
    public function guestCanNotAccessAuthenticatedHomePage()
    {
        $response = $this->get('/home');

        $response->assertStatus(302);
        $response->assertLocation('/login');
    }



}
