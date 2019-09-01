<?php

namespace App\Http\Controllers;

use App\Aisle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AisleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $aisles = Aisle::all();

        return view('aisles.index', compact('aisles'));
    }

    /**
     * Store the resource.
     *
     * @param  Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $updatedAisles = collect($request->all())->pluck('id');
        $user = Auth::user();
        $user->aisles = $updatedAisles;
        $user->save();

        return view('aisles.index', compact('aisles'));
    }

}
