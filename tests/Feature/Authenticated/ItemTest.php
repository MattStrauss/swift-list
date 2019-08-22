<?php

namespace Tests\Feature;

use App\Aisle;
use App\Item;
use App\Recipe;
use App\ShoppingList;
use App\User;
use Tests\TestCase;
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

    /** @test */
    public function userCanDeleteTheirOwnItem()
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/items/'. $item->id);

        $response->assertOk();
        $this->assertDatabaseMissing('items',
            [ 'user_id' => $user->id, 'id' => $item->id]);
    }

    /** @test */
    public function userCanDeleteTheirOwnItemAndItCascadeDeletesCorrectly()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);
        $recipe = factory(Recipe::class)->create(['user_id' => $user->id]);
        $itemToBeDeleted = factory(Item::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $recipeItems = collect([$itemToBeDeleted, $item1, $item2, $item3]);
        $recipe->items()->sync($recipeItems->pluck('id'));


        $item4 = factory(Item::class)->create(['user_id' => $user->id]);
        $item5 = factory(Item::class)->create(['user_id' => $user->id]);
        $item6 = factory(Item::class)->create(['user_id' => $user->id]);
        $shoppingListItems = collect([$itemToBeDeleted, $item4, $item5, $item6]);
        $shopping_list->items()->sync($shoppingListItems->pluck('id'));

        $response = $this->actingAs($user)->delete('/items/'. $itemToBeDeleted->id);

        $response->assertOk();
        $this->assertDatabaseMissing('items', ['user_id' => $user->id, 'id' => $itemToBeDeleted->id]);

        $this->assertDatabaseMissing('item_recipe', ['item_id' => $itemToBeDeleted->id, 'recipe_id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item1->id, 'recipe_id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item2->id, 'recipe_id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item3->id, 'recipe_id' => $recipe->id]);

        $this->assertDatabaseMissing('item_shopping_list', ['item_id' => $itemToBeDeleted->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item4->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item5->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item6->id, 'shopping_list_id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanNotDeleteAnotherUsersItem()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $item = factory(Item::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete('/items/'. $item->id);

        $response->assertForbidden();
        $this->assertDatabaseHas('items',
            [ 'user_id' => $otherUser->id, 'id' => $item->id]);
    }
}
