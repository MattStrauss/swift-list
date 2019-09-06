@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <items-index :initial-available-items='@json($items)' :aisles='@json($aisles)' :custom-aisle-oreder='@json(Auth::user()->aisle_order)' ></items-index>

            </div>
        </div>
    </div>
@endsection

