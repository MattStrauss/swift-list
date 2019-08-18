@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <shopping-list-form
                        :initial-list='@json($shopping_list)'
                        :initial-included-recipes='@json($shopping_list->recipes)'
                        :available-recipes='@json($recipes)'
                        :initial-included-items='@json($shopping_list->items)'
                        :available-items='@json($items)'
                        :aisles='@json($aisles)'
                        :initial-action='@json('Update')'
                        :previous-url = '@json(URL::previous())'>
                    </shopping-list-form>
                </div>
            </div>
        </div>
    </div>
@endsection
