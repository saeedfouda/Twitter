@extends('layouts.app')

@section('title', 'Login on Twitter')

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
    @if(count($errors))
        <div class="alert-message">
            <div class="card">
                <div class="card-body" style="border-top: 0;">
                    <i class="fa fa-times" onclick="$('.alert-message').hide('fast', function(){$(this).remove()})"></i>
                    The username and password that you entered did not match our records. Please double-check and try again.<br />
                </div>
            </div>
        </div>
    @endif
    <div class="auth">
        <div class="container">
            <div class="card">
                <form action="{{url('/login')}}" method="POST" class="card-body">
                    <h3>Log in to Twitter</h3>
                    <input type="text" name="email" value="{{old('email')}}" placeholder="Phone, email or username">
                    <input type="password" name="password" value="{{old('password')}}" placeholder="Password">
                    {{csrf_field()}}
                    <input type="submit" value="Log in">
                    <input type="checkbox" name="remember" id="remember" checked>
                    <label for="remember">Remember me</label>
                    <a href="{{route('password.request')}}">Forgot password?</a>
                </form>
                <div class="card-foot">
                    <p>
                        New to Twitter?
                        <a href="{{route('register')}}">
                            Sign up now
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
