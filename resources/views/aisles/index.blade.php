@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Aisle Order</div>

                    <aisles-index :initial-aisles='@json($aisles)'></aisles-index>

                </div>

            </div>
        </div>
    </div>
@endsection
