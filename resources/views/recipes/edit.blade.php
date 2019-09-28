@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">

                    <recipe-form :initial-recipe='@json($recipe)' :initial-items='@json($items)' :available-items='@json($availableItems)' :categories='@json($categories)'
                                 :aisles='@json($aisles)'  :previous-url= '@json(URL::previous())' :initial-action='@json('Update')' @open-item-modal="modalItemOpen = true" >
                    </recipe-form>

                </div>

            </div>
        </div>
    </div>
@endsection

