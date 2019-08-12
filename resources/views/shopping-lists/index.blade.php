@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Shopping Lists
                        <a href="{{route('shopping-lists.create')}}">
                            <button type="button" style="margin-right:10px;" class="btn btn-sm btn-outline-primary float-right"><i class="fa fa-fs fa-plus-circle"></i> New List</button>
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


                        @if($shopping_lists->isEmpty())

                            <h3>No Lists Yet!</h3>

                        @else

                            <ul class="list-group list-group-flush">

                                @foreach($shopping_lists as $shopping_list)

                                        <a href="/shopping-lists/{{$shopping_list->id}}" class="list-group-item list-group-item-action">
                                            {{ $shopping_list->name }}
                                            <span class="small text-muted">
                                                ({{ $shopping_list->created_at->format('Y') }})
                                            </span>
                                        </a>

                                @endforeach

                            </ul>

                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
