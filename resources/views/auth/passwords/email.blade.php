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
                <form method="POST" action="{{ route('password.options') }}" class="card-body">
                    <h3>Find your Twitter account</h3>
                    <div class="form-group">
                        <input type="text" name="credintial" value="{{old('credintial')}}" placeholder="Phone, email or username">
                        @if($errors->has('credintial'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{$errors->first('credintial')}}</strong>
                            </span>
                        @endif
                    </div>
                    {{csrf_field()}}
                    <input type="submit" value="Search">
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
