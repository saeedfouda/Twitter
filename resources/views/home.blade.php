@extends('layouts.app')

@section('title', 'Twitter')

@section('styles')
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <script src="{{ asset('js/home.js') }}" defer></script>
@endsection

@section('content')
    @include('layouts.navbar')
    <!-- Layout -->
    <div class="home" id="{{Auth::id()}}">
        <div class="container">
            <div class="row">
                <div class="d-xs-none d-sm-none d-md-block col-md-5 col-lg-3 left-bar">
                    <!-- Profile Cards -->
                    <div class="profileCard">
                        <a href="{{route('user.show', Auth::user()->username)}}" class="cover" {{ !is_null(Auth::user()->cover) ? "style=background:url('" . asset('/storage/public/covers/' . Auth::user()->cover) . "')": ''}}></a>
                        <div class="profileContent">
                            <a href="{{route('user.show', Auth::user()->username)}}">
                                <div class="avatar" {{!is_null(Auth::user()->photo) ? "style=background:url('" . asset('/storage/public/photos/' . Auth::user()->photo) . "')" : ''}}></div>
                                <div class="userDetails">
                                    <span class="name">{{Auth::user()->name}}</span>
                                    <span class="username">{{Auth::user()->username}}</span>
                                </div>
                            </a>
                            <div class="userStats row row-eq-height">
                                <div class="col-sm-6">
                                    <span class="mini-title">Tweets<span>
                                    <span class="value">{{Auth::user()->tweets()->count()}}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="mini-title">Following<span>
                                    <span class="value">{{Auth::user()->following()->count()}}</span>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Profile Cards -->

                </div>

                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-6 content">

                    <!-- New Post -->
                    <div class="publish">
                        <div class="photo">
                            <img src="{{ !is_null(Auth::user()->photo) ? asset('/storage/public/photos/' . Auth::user()->photo) : env('DEFAULT_PHOTO')}}">
                        </div>
                        <textarea type="text" placeholder="What's happening?" maxlength="280" :class='{"has-data": (tweet.length > 0)}' v-model="tweet"></textarea>
                        <span class="counter" v-text="counter"></span>
                        <button class="btn" :disabled="!canTweet || disabled" @click="publish">Tweet</button>
                    </div><!-- New Post -->

                    <!-- All Tweets -->
                    <div class="posts" v-if="tweets.length">
                        <!-- Single Tweet -->
                        <div class="post" v-for="(tweet, key) in tweets" :id="tweet.id">

                            <!-- Dropdown of actions -->
                            <div v-if="tweet.user.id === {{Auth::id()}}">
                                <i class="fa fa-angle-down" id="dropdownMenuI" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuI">
                                    <a class="dropdown-item" @click="deleteTweet(key)">Delete Tweet</a>
                                </div>
                            </div>
                            <!-- Dropdown of actions -->

                            <div class="postContent">
                                <a :href="'{{route('user.show', '/')}}' + '/' + tweet.user.username" class="user">
                                    <img :src="(tweet.user.photo) ? '{{asset('/storage/public/photos')}}' + '/' + tweet.user.photo: 'https://abs.twimg.com/sticky/default_profile_images/default_profile_normal.png'">
                                </a>
                                <div class="data">
                                    <a :href="'{{route('user.show', '/')}}/' + tweet.user.username" class="name" v-text="tweet.user.name"></a>
                                    <a :href="'{{route('user.show', '/')}}/' + tweet.user.username" class="username" v-text="tweet.user.username"></a>
                                    <a :href="'{{url('/')}}/' + tweet.user.username + '/status/' + tweet.id" class="date" v-text="dateFormat(tweet.created_at)"></a>
                                </div>
                                <div class="content">
                                    <p v-text="tweet.body"></p>
                                </div>
                                <div class="actions">
                                    <a :href="'{{url('/')}}/' + tweet.user.username + '/status/' + tweet.id" class="action">
                                        <i class="fa fa-comments"></i>
                                        <span v-text="tweet.comments.length"></span>
                                    </a>
                                    <span class="action" :class='{"liked": isLiked(tweet.likes)}' @click="like(key)">
                                        <i class="fa fa-heart"></i>
                                        <span v-text="tweet.likes.length"></span>
                                    </span>
                                    <span class="action">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                </div>
                            </div>
                        </div> <!-- Single Tweet -->
                    </div>
                    <!-- All Tweets -->
                    <div class="no-tweets" v-else>
                        <h3>
                            No tweets to view.
                        </h3>
                        <p>Follow up some users to view their tweets.</p>
                    </div>
                </div>

                <!-- Right Bar -->
                <div class="d-xs-none d-sm-none d-md-none d-lg-block col-lg-3 right-bar">
                    @if(count($suggested))
                        <!-- Suggestions -->
                        <div class="suggestions">
                            <div class="title">Who to follow</div>
                            <div class="content">
                                @foreach($suggested as $suggestion)
                                <!-- Single Suggestion -->
                                <div class="someone">
                                    <a href="{{route('user.show', $suggestion->username)}}">
                                        <img src="{{$suggestion->photo ? asset('/storage/public/photos/' . $suggestion->photo) : env('DEFAULT_PHOTO')}}">
                                        <span class="name" title="{{$suggestion->name}}">{{$suggestion->name}}</span>
                                        <span class="username" title="{{$suggestion->username}}">{{$suggestion->username}}</span>
                                    </a>
                                    <button class="btn follow" @click="follow({{$suggestion->id}}, $event)">Follow</button>
                                </div><!-- Single Suggestion -->
                                @endforeach
                            </div>
                        </div><!-- Suggestions -->
                    @endif
                    <!-- About Website -->
                    <div class="about">
                        <span>&copy; Twitter</span>
                        <a href="#">About</a>
                        <a href="#">Help center</a>
                        <a href="#">Terms</a>
                        <a href="#">Privacy Policy</a>
                        <a href="#">Cookies</a>
                        <a href="#">Ads info</a>
                        <a href="#">Brand</a>
                        <a href="#">Blog</a>
                        <a href="#">Status</a>
                        <a href="#">Apps</a>
                        <a href="#">Jobs</a>
                        <a href="#">Marketing</a>
                        <a href="#">Business</a>
                        <a href="#">Developers</a>
                    </div><!-- About Website -->
                </div>
            </div>
        </div>
    </div>
@endsection
