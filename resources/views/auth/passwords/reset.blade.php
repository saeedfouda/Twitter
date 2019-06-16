@extends('layouts.app')

@section('title', 'Password Reset')

@section('styles')
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    <style>
        .container{
            max-width: 865px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="auth">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('password.request') }}" class="card-body">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <h3>Reset your password</h3>
                    <div class="form-group">
                        <input id="email" type="email" {{ $errors->has('email') ? 'class="is-invalid"' : '' }} name="email" value="{{ $email ?? old('email') }}" placeholder="Your email address" required autofocus>
                        @if($errors->has('email'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{$errors->first('email')}}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <input id="password" type="password" {{ $errors->has('password') ? 'class="is-invalid"' : '' }} name="password" placeholder="New password" required>
                        @if($errors->has('password'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{$errors->first('password')}}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <input id="password-confirm" type="password" name="password_confirmation" placeholder="Confirm your password" required>
                    </div>
                    {{csrf_field()}}
                    <input type="submit" value="Submit">
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            $('.loading-screen').remove();
        });
    </script>
@endsection
