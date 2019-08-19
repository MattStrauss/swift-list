<?php

namespace Tests\Feature;

use App\Aisle;
use App\Item;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function userCanStoreAnItem()
    {
        $user = factory(User::class)->create();
        $aisle = factory(Aisle::class)->create();
        $itemData = ['name' => 'Fish', 'aisle_id' => $aisle->id];

        $response = $this->actingAs($user)->post('/items', $itemData);

        $item = Item::where('name', 'Fish')->where('user_id', $user->id)->first();

        $response->assertOk();
        $this->assertDatabaseHas('items',
            ['name' => 'Fish', 'aisle_id' => $aisle->id, 'user_id' => $user->id, 'id' => $item->id]);
    }

    /** @test */
    public function userCanNotStoreAnItemWithInvalidCredentials_NameMissing()
    {
        $user = factory(User::class)->create();
        $aisle = factory(Aisle::class)->create();
        $itemData = ['name' => '', 'aisle_id' => $aisle->id];

        $response = $this->actingAs($user)->post('/items', $itemData);

        $item = Item::where('name', 'Fish')->where('user_id', $user->id)->first();

        $response->assertRedirect();
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('items',
            ['name' => 'Fish', 'aisle_id' => $aisle->id, 'user_id' => $user->id]);
    }

    /** @test */
    public function userCanNotStoreAnItemWithInvalidCredentials_AisleMissing()
    {
        $user = factory(User::class)->create();
        $aisle = factory(Aisle::class)->create();
        $itemData = ['name' => 'Fish', 'aisle_id' => ''];

        $response = $this->actingAs($user)->post('/items', $itemData);

        $item = Item::where('name', 'Fish')->where('user_id', $user->id)->first();

        $response->assertRedirect();
        $response->assertSessionHasErrors('aisle_id');
        $this->assertDatabaseMissing('items',
            ['name' => 'Fish', 'aisle_id' => $aisle->id, 'user_id' => $user->id]);
    }

    /** @test */
    public function userCanUpdateAnItem()
    {
        $user = factory(User::class)->create();
        $aisle = factory(Aisle::class)->create();
        $item = factory(Item::class)->create(['user_id' => $user->id]);
        $itemData = ['name' => 'Bread', 'aisle_id' => $aisle->id];

        $response = $this->actingAs($user)->put('/items/'. $item->id, $itemData);

        $response->assertOk();
        $this->assertDatabaseHas('items',
            ['name' => 'Bread', 'aisle_id' => $aisle->id, 'user_id' => $user->id, 'id' => $item->id]);
    }

    /** @test */
    public function userCanNotUpdateAnotherUsersItem()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $aisle = factory(Aisle::class)->create();
        $item = factory(Item::class)->create(['user_id' => $otherUser->id]);
        $itemData = ['name' => 'Bread', 'aisle_id' => $aisle->id];

        $response = $this->actingAs($user)->put('/items/'. $item->id, $itemData);

        $response->assertForbidden();
        $this->assertDatabaseMissing('items',
            ['name' => 'Bread', 'aisle_id' => $aisle->id, 'user_id' => $otherUser->id, 'id' => $item->id]);
    }
}
