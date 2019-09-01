@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <div class="list-group">
                            <a href="{{route('recipes.index')}}" class="list-group-item list-group-item-action">Recipes</a>
                            <a href="{{route('shopping-lists.index')}}" class="list-group-item list-group-item-action">Shopping Lists</a>
                            <a href="{{route('items.index')}}" class="list-group-item list-group-item-action">Items</a>
                            <a href="{{route('aisles.index')}}" class="list-group-item list-group-item-action">Aisle Order</a>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
