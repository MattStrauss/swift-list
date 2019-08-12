<?php

namespace App\Http\Controllers;

use App\ShoppingList;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     *
     * @return void
     */
    public function show(ShoppingList $shopping_list)
    {
        $shopping_list = ShoppingList::where('id', $shopping_list->id)->with(['recipes.category', 'recipes.items', 'recipes.items.aisle', 'items.aisle'])->first();

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
