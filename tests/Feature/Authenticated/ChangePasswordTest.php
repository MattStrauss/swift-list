<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function userCanChangePassword()
    {
        $user = factory(User::class)->create();
        $data = ['current_password' => 'password', 'new_password' => 'password1', 'new_password_confirmation' => 'password1'];

        $response = $this->actingAs($user)->put('/change-password', $data);

        $response->assertStatus(302);
        $this->assertTrue(Hash::check('password1', $user->password));
    }

    /** @test */
    public function userCanNotChangePasswordWithoutKnowingCurrentPassword()
    {
        $user = factory(User::class)->create();
        $data = ['current_password' => 'password5', 'new_password' => 'password1', 'new_password_confirmation' => 'password1'];

        $response = $this->actingAs($user)->put('/change-password', $data);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('current_password');
        $this->assertFalse(Hash::check('password1', $user->password));
    }

    /** @test */
    public function userCanNotChangePasswordIfNewPasswordNotConfirmedInSecondTextBox()
    {
        $user = factory(User::class)->create();
        $data = ['current_password' => 'password', 'new_password' => 'password1', 'new_password_confirmation' => 'password2'];

        $response = $this->actingAs($user)->put('/change-password', $data);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('new_password');
        $this->assertFalse(Hash::check('password1', $user->password));
    }

    /** @test */
    public function userCanNotChangePasswordIfNewPasswordIsLessThanEightCharacters()
    {
        $user = factory(User::class)->create();
        $data = ['current_password' => 'password', 'new_password' => 'pass', 'new_password_confirmation' => 'pass'];

        $response = $this->actingAs($user)->put('/change-password', $data);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('new_password');
        $this->assertFalse(Hash::check('password1', $user->password));
    }
}
