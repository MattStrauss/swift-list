@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">

                    <recipe-form :initial-recipe='{}' :initial-items='[]' :categories = '@json($categories)' :previous-url = '@json(URL::previous())' :action='@json('Add')'
                                 @open-item-modal="modalItemOpen = true">

                    </recipe-form>

                </div>

                <modal-item :initial-item='{}' :initial-action='@json('Add')' :aisles='@json($aisles)' v-show="modalItemOpen" @close-item-modal="modalItemOpen = false"></modal-item>

            </div>
        </div>
    </div>
@endsection
