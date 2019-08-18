@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <shopping-list-form
                        :initial-list-name='@json(\Carbon\Carbon::now()->format("l, M jS"). " List")'
                        :initial-included-recipes='@json([])'
                        :available-recipes='@json($recipes)'
                        :initial-included-items='@json([])'
                        :available-items='@json($items)'
                        :aisles='@json($aisles)'
                        :action='@json('Create')'
                        :previous-url = '@json(URL::previous())'>
                    </shopping-list-form>
                </div>
            </div>
        </div>
    </div>
@endsection

