@extends('layouts.app')

@section('title', 'Twitter')

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
                <form class="card-body" method="POST" action="{{ route('register') }}" aria-label="{{ __('Register') }}">
                    <h3>Create your account</h3>
                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" placeholder="Name" required autofocus>
                    @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif

                    <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" placeholder="Username" required autofocus>
                    @if ($errors->has('username'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('username') }}</strong>
                        </span>
                    @endif

                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="Email" required>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif

                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required>
                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                    {{csrf_field()}}
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm password" required>

                    <input type="submit" value="Sign up">
                </form>
                <div class="card-foot">
                    <p>
                        Already a user?
                        <a href="{{route('login')}}">
                            Login
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </p>
                </div>
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
