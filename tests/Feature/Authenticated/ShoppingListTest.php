<?php

namespace Tests\Feature;

use App\ShoppingList;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShoppingListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function userCanViewTheirOwnShoppingLists()
    {
        $user = factory(User::class)->create();
        $shoppingList = factory(ShoppingList::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/shopping-lists/'. $shoppingList->id);

        $response->assertOk();
        $response->assertViewHas(['shopping_list', 'items']);
    }

    /** @test */
    public function userCanNotViewOtherUsersShoppingLists()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $shoppingList = factory(ShoppingList::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/shopping-lists/'. $shoppingList->id);

        $response->assertForbidden();
    }
}
