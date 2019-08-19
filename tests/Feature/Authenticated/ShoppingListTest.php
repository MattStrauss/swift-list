<?php

namespace Tests\Feature;

use App\Item;
use App\Recipe;
use App\ShoppingList;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShoppingListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function userCanAccessShoppingListsIndex()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/shopping-lists');

        $response->assertOk();
        $response->assertViewHas('shopping_lists');
    }

    /** @test */
    public function userCanViewTheirOwnShoppingLists()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/shopping-lists/'. $shopping_list->id);

        $response->assertOk();
        $response->assertViewHas(['shopping_list', 'items']);
    }

    /** @test */
    public function userCanNotViewOtherUsersShoppingLists()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/shopping-lists/'. $shopping_list->id);

        $response->assertForbidden();
    }

    /** @test */
    public function userCanVisitTheCreateShoppingListsPage()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/shopping-lists/create');

        $response->assertOk();
        $response->assertViewHas(['shopping_list', 'recipes', 'items', 'aisles']);
    }

    /** @test */
    public function userCanStoreAShoppingList()
    {
        $user = factory(User::class)->create();
        $shoppingListData = ['name' => 'New List'];

        $response = $this->actingAs($user)->post('/shopping-lists', $shoppingListData);

        $shopping_list = ShoppingList::where('name', 'New List')->where('user_id', $user->id)->first();

        $response->assertOk();
        $this->assertDatabaseHas('shopping_lists',
            ['name' => 'New List', 'user_id' => $user->id, 'id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanStoreAShoppingListWithItems()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $items = collect([$item1, $item2, $item3]);
        $shoppingListData = ['name' => 'Another List', 'items' => $items];

        $response = $this->actingAs($user)->post('/shopping-lists', $shoppingListData);

        $shopping_list = ShoppingList::where('name', 'Another List')->where('user_id', $user->id)->first();

        $response->assertOk();
        $this->assertDatabaseHas('shopping_lists', ['name' => 'Another List', 'user_id' => $user->id, 'id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item3->id, 'shopping_list_id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanStoreAShoppingListWithRecipes()
    {
        $user = factory(User::class)->create();
        $recipe1 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe2 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe3 = factory(Recipe::class)->create(['user_id' => $user->id]);

        $recipes = collect([$recipe1, $recipe2, $recipe3]);
        $shoppingListData = ['name' => 'Another List', 'recipes' => $recipes];

        $response = $this->actingAs($user)->post('/shopping-lists', $shoppingListData);

        $shopping_list = ShoppingList::where('name', 'Another List')->where('user_id', $user->id)->first();

        $response->assertOk();
        $this->assertDatabaseHas('shopping_lists', ['name' => 'Another List', 'user_id' => $user->id, 'id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe3->id, 'shopping_list_id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanStoreAShoppingListWithRecipesAndItems()
    {
        $user = factory(User::class)->create();
        $recipe1 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe2 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe3 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $items = collect([$item1, $item2, $item3]);
        $recipes = collect([$recipe1, $recipe2, $recipe3]);
        $shoppingListData = ['name' => 'Another List', 'recipes' => $recipes, 'items' => $items];

        $response = $this->actingAs($user)->post('/shopping-lists', $shoppingListData);

        $shopping_list = ShoppingList::where('name', 'Another List')->where('user_id', $user->id)->first();

        $response->assertOk();
        $this->assertDatabaseHas('shopping_lists', ['name' => 'Another List', 'user_id' => $user->id, 'id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe3->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item3->id, 'shopping_list_id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanStoreAShoppingListWithOutRecipesOrItems()
    {
        $user = factory(User::class)->create();
        $items = collect([]);
        $recipes = collect([]);
        $shoppingListData = ['name' => 'Another List', 'recipes' => $recipes, 'items' => $items];

        $response = $this->actingAs($user)->post('/shopping-lists', $shoppingListData);

        $shopping_list = ShoppingList::where('name', 'Another List')->where('user_id', $user->id)->first();

        $response->assertOk();
        $this->assertDatabaseHas('shopping_lists', ['name' => 'Another List', 'user_id' => $user->id, 'id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanNotStoreAShoppingListWithInvalidCredentials()
    {
        $user = factory(User::class)->create();
        $shoppingListData = ['name' => ''];

        $response = $this->actingAs($user)->post('/shopping-lists', $shoppingListData);

        $response->assertRedirect();
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('shopping_lists',
            ['name' => '', 'user_id' => $user->id]);
    }

    /** @test */
    public function userCanEditTheirOwnShoppingList()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/shopping-lists/'. $shopping_list->id. '/edit');

        $response->assertOk();
        $response->assertViewHas(['shopping_list', 'recipes', 'items', 'aisles']);
    }

    /** @test */
    public function userCanNotEditOtherUsersShoppingList()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/shopping-lists/'. $shopping_list->id. '/edit');

        $response->assertForbidden();
    }

    /** @test */
    public function userCanUpdateTheirOwnShoppingList()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);
        $shoppingListData = ['name' => 'Yet, Another List'];

        $response = $this->actingAs($user)->put('/shopping-lists/'. $shopping_list->id, $shoppingListData);

        $response->assertOk();
        $this->assertDatabaseHas('shopping_lists', ['name' => 'Yet, Another List', 'user_id' => $user->id, 'id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanUpdateTheirOwnShoppingListWithRecipes()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);
        $recipe1 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe2 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe3 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipes = collect([$recipe1, $recipe2, $recipe3]);
        $shoppingListData = ['name' => 'Another List', 'recipes' => $recipes];

        $response = $this->actingAs($user)->put('/shopping-lists/'. $shopping_list->id, $shoppingListData);

        $response->assertOk();
        $this->assertDatabaseHas('shopping_lists', ['user_id' => $user->id, 'id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe3->id, 'shopping_list_id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanUpdateTheirOwnShoppingListWithItems()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $items = collect([$item1, $item2, $item3]);
        $shoppingListData = ['name' => 'Another List', 'items' => $items];

        $response = $this->actingAs($user)->put('/shopping-lists/'. $shopping_list->id, $shoppingListData);

        $response->assertOk();
        $this->assertDatabaseHas('shopping_lists', ['user_id' => $user->id, 'id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item3->id, 'shopping_list_id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanUpdateTheirOwnShoppingListWithRecipesAndItems()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);
        $recipe1 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe2 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe3 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $items = collect([$item1, $item2, $item3]);
        $recipes = collect([$recipe1, $recipe2, $recipe3]);
        $shoppingListData = ['name' => 'Another List', 'recipes' => $recipes, 'items' => $items];

        $response = $this->actingAs($user)->put('/shopping-lists/'. $shopping_list->id, $shoppingListData);

        $response->assertOk();
        $this->assertDatabaseHas('shopping_lists', ['user_id' => $user->id, 'id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe3->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item3->id, 'shopping_list_id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanUpdateTheirOwnShoppingListByRemovingRecipesAndItems()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);
        $recipe1 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe2 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe3 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $originalItems = collect([$item1, $item2, $item3]);
        $originalRecipes = collect([$recipe1, $recipe2, $recipe3]);

        $shopping_list->items()->sync($originalItems->pluck('id'));
        $shopping_list->recipes()->sync($originalRecipes->pluck('id'));

        $updatedItems = collect([$item2, $item3]);
        $updatedRecipes = collect([$recipe1, $recipe3]);

        $shoppingListData = ['name' => 'Another List', 'recipes' => $updatedRecipes, 'items' => $updatedItems];

        $response = $this->actingAs($user)->put('/shopping-lists/'. $shopping_list->id, $shoppingListData);


        $response->assertOk();
        $this->assertDatabaseHas('shopping_lists', ['user_id' => $user->id, 'id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseMissing('recipe_shopping_list', ['recipe_id' => $recipe2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe3->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseMissing('item_shopping_list', ['item_id' => $item1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item3->id, 'shopping_list_id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanNotUpdateTheirOwnShoppingListWithInvalidCredentials()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);
        $shoppingListData = ['name' => ''];

        $response = $this->actingAs($user)->put('/shopping-lists/'. $shopping_list->id, $shoppingListData);

        $response->assertRedirect();
        $this->assertDatabaseMissing('shopping_lists', ['name' => '', 'user_id' => $user->id, 'id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanNotUpdateAnotherUsersShoppingList()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $otherUser->id]);
        $shoppingListData = ['name' => 'Other Users List'];

        $response = $this->actingAs($user)->put('/shopping-lists/'. $shopping_list->id, $shoppingListData);

        $response->assertForbidden();
        $this->assertDatabaseMissing('shopping_lists', ['name' => 'Other Users List', 'user_id' => $otherUser->id, 'id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanDeleteTheirOwnShoppingList()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/shopping-lists/'. $shopping_list->id);

        $response->assertOk();
        $this->assertDatabaseMissing('shopping_lists', ['user_id' => $user->id, 'id' => $shopping_list->id]);
    }

    /** @test */
    public function userCanDeleteTheirOwnShoppingListAndItCascadeDeletesCorrectly()
    {
        $user = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $user->id]);
        $recipe1 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe2 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe3 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $originalItems = collect([$item1, $item2, $item3]);
        $originalRecipes = collect([$recipe1, $recipe2, $recipe3]);

        $shopping_list->items()->sync($originalItems->pluck('id'));
        $shopping_list->recipes()->sync($originalRecipes->pluck('id'));

        $shopping_list2 = factory(ShoppingList::class)->create(['user_id' => $user->id]);
        $recipe4 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe5 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $recipe6 = factory(Recipe::class)->create(['user_id' => $user->id]);
        $item4 = factory(Item::class)->create(['user_id' => $user->id]);
        $item5 = factory(Item::class)->create(['user_id' => $user->id]);
        $item6 = factory(Item::class)->create(['user_id' => $user->id]);
        $items = collect([$item4, $item5, $item6]);
        $recipes = collect([$recipe4, $recipe5, $recipe6]);

        $shopping_list2->items()->sync($items->pluck('id'));
        $shopping_list2->recipes()->sync($recipes->pluck('id'));

        $response = $this->actingAs($user)->delete('/shopping-lists/'. $shopping_list->id);

        $response->assertOk();
        $this->assertDatabaseMissing('shopping_lists', ['user_id' => $user->id, 'id' => $shopping_list->id]);
        $this->assertDatabaseMissing('recipe_shopping_list', ['recipe_id' => $recipe1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseMissing('recipe_shopping_list', ['recipe_id' => $recipe2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseMissing('recipe_shopping_list', ['recipe_id' => $recipe3->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseMissing('item_shopping_list', ['item_id' => $item1->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseMissing('item_shopping_list', ['item_id' => $item2->id, 'shopping_list_id' => $shopping_list->id]);
        $this->assertDatabaseMissing('item_shopping_list', ['item_id' => $item3->id, 'shopping_list_id' => $shopping_list->id]);

        $this->assertDatabaseHas('shopping_lists', ['user_id' => $user->id, 'id' => $shopping_list2->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe4->id, 'shopping_list_id' => $shopping_list2->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe5->id, 'shopping_list_id' => $shopping_list2->id]);
        $this->assertDatabaseHas('recipe_shopping_list', ['recipe_id' => $recipe6->id, 'shopping_list_id' => $shopping_list2->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item4->id, 'shopping_list_id' => $shopping_list2->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item5->id, 'shopping_list_id' => $shopping_list2->id]);
        $this->assertDatabaseHas('item_shopping_list', ['item_id' => $item6->id, 'shopping_list_id' => $shopping_list2->id]);
    }

    /** @test */
    public function userCanNotDeleteAnotherUsersShoppingList()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $shopping_list = factory(ShoppingList::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete('/shopping-lists/'. $shopping_list->id);

        $response->assertForbidden();
        $this->assertDatabaseHas('shopping_lists', ['user_id' => $otherUser->id, 'id' => $shopping_list->id]);
    }
}
