<?php

namespace App\Http\Controllers;

use App\Aisle;
use App\Category;
use App\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Recipe::class, 'recipe');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recipes = Auth::user()->recipes()->with('category:id,name')->get()->sortBy('category.name')->values();

        return view('recipes.index', compact('recipes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $aisles = Aisle::all();

        $categories = Category::all();

        $availableItems = Auth::user()->items()->with(['aisle'])->get();

        return view('recipes.create', compact('categories', 'aisles', 'availableItems'));
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
            'category_id' => 'required',
            'instructions' => 'required',
        ]);

        $request->merge(['user_id' => Auth::user()->id]);
        $recipe = Recipe::create($request->all());

        $items = collect($request->input('items'))->pluck('id');
        $recipe->items()->sync($items);

        session()->flash('status', ['type' => 'primary', 'message' => 'Recipe successfully created'] );
        $redirect =  ['redirect' => route('recipes.index')];

        return response()->json($recipe, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  Recipe  $recipe
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Recipe $recipe)
    {
        $items = $recipe->items()->with(['aisle'])->get()->sortBy('name');

        return view('recipes.show', compact('recipe', 'items'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Recipe  $recipe
     *
     * @return void
     */
    public function edit(Recipe $recipe)
    {
        $items = $recipe->items()->with(['aisle'])->get();

        $aisles = Aisle::all();

        $categories = Category::all();

        $availableItems = Auth::user()->items()->with(['aisle'])->get();

        return view('recipes.edit', compact('recipe', 'items', 'categories', 'aisles', 'availableItems'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Recipe  $recipe
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Recipe $recipe)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'category_id' => 'required',
            'instructions' => 'required',
        ]);

        $recipe->update($request->all());

        $items = collect($request->input('items'))->pluck('id');
        $recipe->items()->sync($items);

        return response()->json($recipe, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Recipe  $recipe
     *
     * @return mixed
     * @throws \Exception
     */
    public function destroy(Recipe $recipe)
    {
        if (! $recipe->delete()) {

            $error = ['error' => 'Error deleting recipe, please try again.'];

            return response()->json($error, 422);
        }

        session()->flash('status', ['type' => 'primary', 'message' => 'Recipe successfully deleted!'] );
        $redirect =  ['redirect' => route('recipes.index')];

        return response()->json($redirect, 200);
    }
}
