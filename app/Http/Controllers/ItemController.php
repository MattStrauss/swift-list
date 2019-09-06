<?php

namespace App\Http\Controllers;

use App\Aisle;
use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Item::class, 'item');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $items = $user->items()->with(['aisle'])->get();
        $aisles = Aisle::withCustomOrder($user);

        return view('items.index', compact('items', 'aisles'));
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
            'aisle_id' => 'required',
        ]);

        $user = Auth::user();
        $name = $request->input('name');
        $aisle_id = $request->input('aisle_id');

        $item = Item::with('aisle')->where('name', $name)->where('user_id', $user->id)->where('aisle_id', $aisle_id)->first();
        if (! $item ){
            $item = Item::create(['name' => $name, 'aisle_id' => $aisle_id, 'user_id' => $user->id]);

            $item = Item::with('aisle')->find($item->id);
        }

        return response()->json($item, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Item  $item
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Item $item)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'aisle_id' => 'required',
        ]);

        $item->update($request->all());

        $item = Item::with('aisle')->find($item->id);

        return response()->json($item, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Item  $item
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Item $item)
    {
        if (! $item->delete()) {

            $error = ['error' => 'Error deleting item, please try again.'];

            return response()->json($error, 422);
        }

        return response()->json(null, 200);
    }
}
