<?php

namespace App\Http\Controllers;

use App\Aisle;
use App\ShoppingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Collection;

class ShoppingListController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(ShoppingList::class,'shopping_list');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shopping_lists = Auth::user()->shoppingLists()->latest()->get();

        return view('shopping-lists.index', compact('shopping_lists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        $recipes = $user->recipes()->with(['category'])->get();
        $items = $user->items()->with(['aisle'])->get();
        $items = Aisle::applyCustomAisleOrderForJavascriptRendering($items);
        $aisles = Aisle::withCustomOrder($user);
        $shopping_list = ['id' => '', 'name' => Carbon::now()->format("l, M jS"). " List"];

        return view('shopping-lists.create', compact('recipes', 'items', 'aisles', 'shopping_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);

        $shopping_list = new ShoppingList();
        $shopping_list->name = $request->input('name');
        $shopping_list->user()->associate(Auth::user()->id);
        $shopping_list->save();

        $items = collect($request->input('items'))->pluck('id');
        $shopping_list->items()->sync($items);

        $recipes = collect($request->input('recipes'))->pluck('id');
        $shopping_list->recipes()->sync($recipes);

        return response()->json($shopping_list, 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  ShoppingList  $shopping_list
     *
     * @return void
     */
    public function show(ShoppingList $shopping_list)
    {
        $shopping_list->with(['recipes.category', 'recipes.items.aisle', 'items.aisle'])->get();

        $listItems = $shopping_list->items;

        $recipeItems = collect();
        $shopping_list->recipes->each(function ($recipe) use ($recipeItems) {
            $recipeItems->push($recipe->items);
        });

        $items = $this->combineListAndRecipeItems($recipeItems, $listItems);

        return view('shopping-lists.show', compact('shopping_list', 'items'));
    }

    /**
     * Combine items and recipe items into single collection grouped by aisle
     * If the user has set a custom aisle order, apply it to the items ordering
     *
     * @param $recipeItems
     * @param $listItems
     *
     * @return Collection
     */
    private function combineListAndRecipeItems($recipeItems, $listItems)
    {
        $items = $recipeItems->push($listItems)->collapse()->groupBy('aisle_id')->each(function ($aisle) {

            $aisleDuplicates = $aisle->duplicates('name');
            $occurrenceCounts = array_count_values($aisleDuplicates->toArray());

            $aisle->forget($aisleDuplicates->keys()->toArray())->each(function ($item) use ($aisleDuplicates, $occurrenceCounts){
                if (in_array($item->name, $aisleDuplicates->toArray())) {
                    $item->name = $item->name . ' ('. (1 + $occurrenceCounts[$item->name]).')';
                }

                return $item;
            });
        });

        $items = Aisle::applyCustomAisleOrder($items);

        return $items;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ShoppingList  $shopping_list
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ShoppingList $shopping_list)
    {
        $user = Auth::user();

        $recipes = $user->recipes()->with(['category'])->get();
        $items = $user->items()->with(['aisle'])->get();
        $items = Aisle::applyCustomAisleOrderForJavascriptRendering($items);
        $aisles = Aisle::withCustomOrder($user);

        return view('shopping-lists.edit', compact('recipes', 'items', 'aisles', 'shopping_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ShoppingList  $shopping_list
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, ShoppingList $shopping_list)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);

        $shopping_list->update($request->all());

        $items = collect($request->input('items'))->pluck('id');
        $shopping_list->items()->sync($items);

        $recipes = collect($request->input('recipes'))->pluck('id');
        $shopping_list->recipes()->sync($recipes);

        return response()->json($shopping_list, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ShoppingList  $shopping_list
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(ShoppingList $shopping_list)
    {
        if (! $shopping_list->delete()) {

            $error = ['error' => 'Error deleting shopping list, please try again.'];

            return response()->json($error, 422);
        }

        session()->flash('status', ['type' => 'primary', 'message' => 'Shopping List successfully deleted!'] );
        $redirect =  ['redirect' => route('shopping-lists.index')];

        return response()->json($redirect, 200);
    }
}
