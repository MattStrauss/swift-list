<?php

namespace App\Http\Controllers;

use App\Aisle;
use App\ShoppingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;

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
        $aisles = Aisle::all();
        $list = ['id' => '', 'name' => Carbon::now()->format("l, M jS"). " List"];

        return view('shopping-lists.create', compact('recipes', 'items', 'aisles', 'list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);

        $list = new ShoppingList();
        $list->name = $request->input('name');
        $list->user()->associate(Auth::user()->id);
        $list->save();

        $items = collect($request->input('items'))->pluck('id');
        $list->items()->sync($items);

        $recipes = collect($request->input('recipes'))->pluck('id');
        $list->recipes()->sync($recipes);

        return response()->json($list, 200);

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

        $items = $recipeItems->push($listItems)->collapse()->groupBy('aisle_id')->each(function($aisle) {

            $aisleDuplicates = $aisle->duplicates('name');
            $occurrenceCounts = array_count_values($aisleDuplicates->toArray());
            $aisle->forget($aisleDuplicates->keys()->toArray());

            foreach ($aisleDuplicates as $duplicate) {
                $aisle[array_search($duplicate, array_column($aisle->toArray(), 'name'))]->name = $duplicate . ' ('. (1 + $occurrenceCounts[$duplicate]).')';
            }
        });

        return view('shopping-lists.show', compact('shopping_list', 'items'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ShoppingList  $shopping_list
     *
     * @return void
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
