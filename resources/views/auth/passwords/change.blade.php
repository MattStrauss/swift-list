@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Change Password</div>

                    @if (session()->has('status'))
                        <div class="alert alert-{{session()->get('status.type')}} fade show alert-dismissible" role="alert" style="margin:2%;">
                            <strong> {{session()->get('status.message')}} </strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="card-body">
                        <form method="POST" action="{{ route('change-password.update') }}">
                            @method('PUT')
                            @csrf

                            <div class="form-group row">
                                <label for="current_password" class="col-md-4 col-form-label text-md-right">Current Password</label>

                                <div class="col-md-6">
                                    <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" value="{{ $email ?? old('email') }}" required autofocus>

                                    @error('current_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="new_password" class="col-md-4 col-form-label text-md-right">New Password</label>

                                <div class="col-md-6">
                                    <input id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" required>

                                    @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-new-confirm" class="col-md-4 col-form-label text-md-right">Confirm New Password</label>

                                <div class="col-md-6">
                                    <input id="password-new-confirm" type="password" class="form-control" name="new_password_confirmation" required>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Update Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
