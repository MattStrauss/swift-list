<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginAndRegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guestCanLogin()
    {
        factory(User::class)->create(['email' => 'test@test.com', 'password' => bcrypt('password')]);

        $loginData = ['email' => 'test@test.com', 'password' => 'password'];

        $response = $this->post('/login', $loginData);

        $response->assertStatus(302);
        $response->assertLocation('/home');
    }

    /** @test */
    public function guestCanNotLoginIfCredentialsAreInvalid()
    {
        factory(User::class)->create(['email' => 'test@test.com', 'password' => bcrypt('password')]);

        $loginData = ['email' => 'test@test.com', 'password' => 'wrongPassword'];

        $response = $this->post('/login', $loginData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }


    /** @test */
    public function guestCanRegister()
    {
        $registerData = ['name' => 'Tom Tester', 'email' => 'test@test.com', 'password' => 'password', 'password_confirmation' => 'password'];

        $response = $this->post('/register', $registerData);

        $response->assertStatus(302);
        $response->assertLocation('/home');
        $this->assertDatabaseHas('users', ['name' => 'Tom Tester', 'email' => 'test@test.com']);
    }

    /** @test */
    public function guestCanNotRegisterWithInvalidCredentials()
    {
        $registerData = ['name' => 'Tom Tester', 'email' => 'test@test.com', 'password' => 'password', 'password_confirmation' => 'differentPassword'];

        $response = $this->post('/register', $registerData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }
}
