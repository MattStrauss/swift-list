@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <items-index :initial-available-items='@json($items)' :aisles='@json($aisles)'></items-index>

            </div>
        </div>
    </div>
@endsection

