@extends('layouts.app')

@section('title', 'Twitter / Notifications')

@section('styles')
    <link href="{{ asset('css/notifications.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2 notifications">
                <div class="title">All</div>
                @if(Auth::user()->notifications->count() > 0)
                    @foreach(Auth::user()->notifications()->orderBy('id', 'DESC')->get() as $notification)
                        <div class="notify">
                            <img class="bird" src="{{asset('images/bird.svg')}}">
                            <p class="what">{!!$notification->notification_html!!}<span class='date'>{{$notification->created_at->format('M d')}}</span></p>
                            @if($notification->tweet)
                                <div class="tweet">
                                    <a href="{{route('user.show', $notification->tweet->user->username)}}" class="name">{{$notification->tweet->user->name}}</a>
                                    <a href="{{route('user.show', $notification->tweet->user->username)}}" class="username">{{$notification->tweet->user->username}}</a>
                                    <a href='{{route('tweet.show', ['username' => $notification->tweet->user->username, 'id' => $notification->tweet->id])}}' class="description">{{$notification->tweet->body}}</a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="notify">
                        <img class="bird" src="{{asset('images/bird.svg')}}">
                        <p class="what lead">You don't have any notifications yet. if you got a notification will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            const app = new Vue({
                el: '#app',
                mounted: function(){
                    $('.loading-screen').remove();
                }
            });
        });
    </script>
@endsection
