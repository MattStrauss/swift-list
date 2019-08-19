@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center" @open-item-modal="modalItemOpen = true">
            <div class="col-md-8">
                <div class="card" >

                    <recipe-form :initial-recipe='@json($recipe)' :initial-items='@json($items)' :available-items='@json($availableItems)' :categories='@json($categories)'
                                 :previous-url= '@json(URL::previous())' :initial-action='@json('Update')' @open-item-modal="modalItemOpen = true"  >
                    </recipe-form>

                </div>

                <modal-item :initial-item='{}' :initial-action='@json('Add')' :aisles='@json($aisles)'  v-show="modalItemOpen" @close-item-modal="modalItemOpen = false"></modal-item>

            </div>
        </div>
    </div>
@endsection

