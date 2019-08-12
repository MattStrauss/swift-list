<?php

namespace Tests\Feature;

use App\Category;
use App\Item;
use App\Recipe;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecipesTest extends TestCase
{
    use RefreshDatabase;
    use withFaker;

    /** @test */
    public function userCanAccessRecipeIndex()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/recipes');

        $response->assertOk();
        $response->assertViewHas('recipes');
    }

    /** @test */
    public function userCanViewTheirOwnRecipe()
    {
        $user = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/recipes/'. $recipe->id);

        $response->assertOk();
        $response->assertViewHas('recipe');
    }

    /** @test */
    public function userCanNotViewOtherUsersRecipe()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/recipes/'. $recipe->id);

        $response->assertForbidden();
    }

    /** @test */
    public function userCanVisitTheCreateRecipePage()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/recipes/create');

        $response->assertOk();
    }

    /** @test */
    public function userCanStoreARecipe()
    {
        $user = factory(User::class)->create();
        $category = factory(Category::class)->create();
        $recipeData = ['name' => 'Fish and Chips', 'category_id' => $category->id, 'instructions' => 'Cook the food...'];

        $response = $this->actingAs($user)->post('/recipes', $recipeData);

        $recipe = Recipe::where('name', 'Fish and Chips')->where('user_id', $user->id)->first();

        $response->assertOk();
        $this->assertDatabaseHas('recipes',
            ['name' => 'Fish and Chips', 'category_id'  => $category->id, 'instructions' => 'Cook the food...', 'user_id' => $user->id, 'id' => $recipe->id]);
    }

    /** @test */
    public function userCanStoreARecipeWithItems()
    {
        $user = factory(User::class)->create();
        $category = factory(Category::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $items = collect([$item1, $item2, $item3]);
        $recipeData = ['name' => 'Fish and Chips', 'category_id' => $category->id, 'instructions' => 'Cook the food...', 'items' => $items];

        $response = $this->actingAs($user)->post('/recipes', $recipeData);

        $recipe = Recipe::where('name', 'Fish and Chips')->where('user_id', $user->id)->first();

        $response->assertOk();
        $this->assertDatabaseHas('recipes', ['name' => 'Fish and Chips', 'user_id' => $user->id, 'id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item1->id, 'recipe_id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item2->id, 'recipe_id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item3->id, 'recipe_id' => $recipe->id]);
    }

    /** @test */
    public function userCanNotStoreRecipeWithInvalidCredentials()
    {
        $user = factory(User::class)->create();
        $category = factory(Category::class)->create();
        $recipeData = ['name' => '', 'category_id' => $category->id, 'instructions' => 'Cook the food...'];

        $response = $this->actingAs($user)->post('/recipes', $recipeData);

        $response->assertRedirect();
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('recipes', ['name' => 'Fish and Chips', 'user_id' => $user->id]);
    }

    /** @test */
    public function userCanEditTheirOwnRecipe()
    {
        $user = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/recipes/'. $recipe->id. '/edit');

        $response->assertOk();
    }

    /** @test */
    public function userCanNotEditOtherUsersRecipe()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/recipes/'. $recipe->id. '/edit');

        $response->assertForbidden();
    }


    /** @test */
    public function userCanUpdateTheirOwnRecipe()
    {
        $user = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $user->id]);
        $category = factory(Category::class)->create();

        $recipeData = ['name' => 'Tacos And Rice', 'category_id' => $category->id, 'instructions' => 'Use the Oven!'];

        $response = $this->actingAs($user)->put('/recipes/'. $recipe->id, $recipeData);

        $response->assertOk();
        $this->assertDatabaseHas('recipes',
            ['name' => 'Tacos And Rice', 'category_id'  => $category->id, 'instructions' => 'Use the Oven!', 'user_id' => $user->id, 'id' => $recipe->id]);
    }

    /** @test */
    public function userCanUpdateTheirOwnRecipeWithItems()
    {
        $user = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $user->id]);

        $category = factory(Category::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $items = collect([$item1, $item2, $item3]);

        $recipeData = ['name' => 'Tacos And Rice', 'category_id' => $category->id, 'instructions' => 'Use the Oven!', 'items' => $items];

        $response = $this->actingAs($user)->put('/recipes/'. $recipe->id, $recipeData);

        $response->assertOk();
        $this->assertDatabaseHas('recipes',
            ['name' => 'Tacos And Rice', 'category_id'  => $category->id, 'instructions' => 'Use the Oven!', 'user_id' => $user->id, 'id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item1->id, 'recipe_id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item2->id, 'recipe_id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item3->id, 'recipe_id' => $recipe->id]);
    }

    /** @test */
    public function userCanUpdateTheirOwnRecipeByRemovingItems()
    {
        $user = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $user->id]);
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $originalItems = collect([$item1, $item2, $item3]);
        $recipe->items()->sync($originalItems->pluck('id'));
        $category = factory(Category::class)->create();

        $updatedItems = collect([$item2, $item3]);

        $recipeData = ['name' => 'Tacos And Rice', 'category_id' => $category->id, 'instructions' => 'Use the Oven!', 'items' => $updatedItems];

        $response = $this->actingAs($user)->put('/recipes/'. $recipe->id, $recipeData);

        $response->assertOk();
        $this->assertDatabaseHas('recipes',
            ['name' => 'Tacos And Rice', 'category_id'  => $category->id, 'instructions' => 'Use the Oven!', 'user_id' => $user->id, 'id' => $recipe->id]);
        $this->assertDatabaseMissing('item_recipe', ['item_id' => $item1->id, 'recipe_id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item2->id, 'recipe_id' => $recipe->id]);
        $this->assertDatabaseHas('item_recipe', ['item_id' => $item3->id, 'recipe_id' => $recipe->id]);
    }

    /** @test */
    public function userCanNotUpdateTheirOwnRecipeWithInvalidCredentials()
    {
        $user = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $user->id]);
        $category = factory(Category::class)->create();

        $recipeData = ['name' => 'Tacos and Rice', 'category_id' => $category->id, 'instructions' => ''];

        $response = $this->actingAs($user)->put('/recipes/'. $recipe->id, $recipeData);

        $response->assertRedirect();
        $response->assertSessionHasErrors('instructions');
        $this->assertDatabaseMissing('recipes',
            ['name' => 'Tacos And Rice', 'category_id'  => $category->id, 'instructions' => '', 'user_id' => $user->id, 'id' => $recipe->id]);
    }

    /** @test */
    public function userCanNotUpdateOtherUsersRecipe()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $otherUser->id]);
        $category = factory(Category::class)->create();

        $recipeData = ['name' => 'Tacos And Rice', 'category_id' => $category->id, 'instructions' => 'Use the Oven!'];

        $response = $this->actingAs($user)->put('/recipes/'. $recipe->id, $recipeData);

        $response->assertForbidden();
        $this->assertDatabaseMissing('recipes',
            ['name' => 'Tacos And Rice', 'category_id'  => $category->id, 'instructions' => 'Use the Oven!', 'user_id' => $otherUser->id, 'id' => $recipe->id]);
    }

    /** @test */
    public function userCanDeleteTheirOwnRecipe()
    {
        $user = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/recipes/'. $recipe->id);

        $response->assertOk();
        $this->assertDatabaseMissing('recipes',
            ['user_id' => $user->id, 'id' => $recipe->id]);
    }

    /** @test */
    public function userCanNotDeleteOtherUsersRecipe()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $recipe = factory(Recipe::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete('/recipes/'. $recipe->id);

        $response->assertForbidden();
        $this->assertDatabaseHas('recipes',
            ['user_id' => $otherUser->id, 'id' => $recipe->id]);
    }
}
