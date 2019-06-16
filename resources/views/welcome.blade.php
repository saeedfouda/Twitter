@extends('layouts.app')

@section('title', 'Twitter. It\'s what\'s happening')

@section('styles')
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
@endsection
@section('body_style', 'padding-bottom: 0 !important;')
@section('content')
    <div class="row">

        <div class="right col-sm-12 col-md-6 order-md-6">
            <div class="btnsBlock">
                <i class="fab fa-twitter"></i>
                <a href="{{route('login')}}" class="login d-xl-none">Log in</a>
                <h2>See what’s happening in the world right now</h2>
                <h4>Join Twitter today.</h4>
                <a href="{{route('register')}}" class="up">
                    <button>Sign Up</button>
                </a>
                <a href="{{route('login')}}" class="in">
                    <button>Log in</button>
                </a>
            </div>

        </div>

        <div class="left col-sm-12 col-md-6 order-md-1">
            <ul>
                <li>
                    <i class="fas fa-search"></i>
                    <span>Follow your interests.</span>
                </li>

                <li>
                    <i class="fas fa-user-friends"></i>
                    <span>Hear what people are talking about</span>
                </li>

                <li>
                    <i class="far fa-comment-alt"></i>
                    <span>Join the conversation.</span>
                </li>
            </ul>
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
