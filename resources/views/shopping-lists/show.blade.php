@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $shopping_list->name }}
                        <a href="{{route('shopping-lists.edit', $shopping_list->id)}}" class="btn btn-sm btn-outline-secondary float-right ml-2"> <i class="fas fa-edit"></i> Edit List</a>
                        <button @click="modalConfirmDeleteOpen = true" type="button" class="btn btn-sm btn-outline-danger float-right"> <i class="fas fa-trash-alt"></i> Delete List</button>
                    </div>

                    <div class="card-body">
                        <h4> Recipes <small><small>({{ $shopping_list->recipes->count() }})</small></small></h4>
                        <ul class="items">
                            @foreach($shopping_list->recipes as $recipe)

                                <a href="/recipes/{{$recipe->id}}"><li class="item">{{$recipe->name}}</li></a>

                            @endforeach

                        </ul>
                        <div class="clearfix"></div> <br>

                        <h4> List</h4>

                            @foreach($items as $aisleItems)

                                <ul class="list-group list-group-flush">

                                    @foreach($aisleItems->sortBy('name') as $item)

                                        @if ($loop->first)

                                            <li class="list-group-item list-group-item-dark">{{ $item->aisle->name }}
                                                <span class="badge badge-light badge-primary badge-pill">{{ $loop->count}}</span>
                                            </li>

                                        @endif

                                            <li class="list-group-item">{{ $item->name }}</li>

                                    @endforeach

                                </ul>

                            @endforeach

                    </div>
                </div>
            </div>
            <modal-confirm-delete :model_type= '@json("shopping-lists")' :model_id= '@json($shopping_list->id)' v-show="modalConfirmDeleteOpen" @close-confirm-delete-modal="modalConfirmDeleteOpen = false">

                <template v-slot:title> Confirm Delete </template>
                <template v-slot:body> Are you sure that you want to delete this shopping list? This action <strong>cannot be undone</strong>.  </template>

            </modal-confirm-delete>
        </div>
    </div>
@endsection
