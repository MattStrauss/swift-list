@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"> {{ $recipe->name }}

                        <a href="{{route('recipes.edit', $recipe->id)}}">
                            <button type="button"  style="margin-right:10px;" class="btn btn-sm btn-outline-secondary float-right ml-2"><i class="fas fa-edit"></i> Edit</button>
                        </a>

                        <button @click="modalConfirmDeleteOpen = true" type="button" class="btn btn-sm btn-outline-danger float-right"><i class="fas fa-trash-alt"></i> Delete</button>

                    </div>

                    <div class="card-body">

                        @unless(! $recipe->instructions)

                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center active">
                                    <h5 class="mb-1">Instructions</h5>
                                </div>

                                <p style="margin:2%;"> {{ $recipe->instructions }} </p>
                            </div>

                        @endunless
                        <div class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center active">
                                <h5 class="mb-1">Ingredients</h5>
                                <span class="badge badge-light badge-pill">{{ count($items) }}</span>
                            </li>

                            @if($items->isNotEmpty())

                                @foreach($items as $item)
                                    <li class="list-group-item">{{ $item->name }}  <small class="text-muted">({{ $item->aisle->name }})</small></li>
                                @endforeach

                            @else

                                <h4>No ingredients...</h4>

                            @endif

                        </div>
                    </div>
                </div>



            </div>
            <modal-confirm-delete :model_type= '@json("recipes")' :model_id= '@json($recipe->id)' v-show="modalConfirmDeleteOpen" @close-confirm-delete-modal="modalConfirmDeleteOpen = false">

                <template v-slot:title> Confirm Delete </template>
                <template v-slot:body> Are you sure that you want to delete this recipe? This action <strong>cannot be undone</strong>.  </template>

            </modal-confirm-delete>
        </div>
    </div>
@endsection
