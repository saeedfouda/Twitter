
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
    @if(count($errors))
        <div class="alert-message">
            <div class="card">
                <div class="card-body" style="border-top: 0;">
                    <i class="fa fa-times" onclick="$('.alert-message').hide('fast', function(){$(this).remove()})"></i>
                    {{$errors->first()}}<br />
                </div>
            </div>
        </div>
    @endif
    <div class="auth">
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('password.email') }}" class="card-body">
                    <h3>How do you want to reset your password?</h3>
                    <p>We found the following information associated with your account.</p>

                    <!-- Email -->
                    <div class="form-group">
                        <input type="radio" name="option" value="0" checked>
                        <label for="option">Email a link to <strong>{{hideEmail(Session::get('resetting_password')[0])}}</strong></label>
                    </div>

                    {{csrf_field()}}
                    <input type="submit" value="Continue">
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
