@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Recipes
                        <a href="{{route('recipes.create')}}">
                            <button type="button" style="margin-right:10px;" class="btn btn-sm btn-outline-primary float-right"><i class="fa fa-plus-circle"></i> New Recipe</button>
                        </a>
                    </div>

                    @if (session()->has('status'))
                        <div class="alert alert-{{session()->get('status.type')}} fade show alert-dismissible" role="alert" style="margin:2%;">
                            <strong> {{session()->get('status.message')}} </strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="card-body">

                        <recipe-listing :recipes='@json($recipes)'></recipe-listing>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
