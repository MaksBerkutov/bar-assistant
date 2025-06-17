@extends('layouts.layout')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">{{ __('Confirm Password') }}</div>

                    <div class="card-body">
                        <p>{{ __('Please confirm your password before continuing.') }}</p>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="mb-3 row">
                                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input type="password" name="password" id="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           required autocomplete="current-password">

                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4 d-flex gap-2 align-items-center">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Confirm Password') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="btn btn-link">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
