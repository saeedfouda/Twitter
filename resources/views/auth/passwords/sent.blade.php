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
                <div class="card-body">
                    <h3>Check your email</h3>
                    <p>We've sent an email to {{hideEmail(Session::get('resetting_password')[0])}}. Click the link in the email to reset your password.</p>
                    <p>If you don't see the email, check other places it might be, like your junk, spam, social or other folders.</p>
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
