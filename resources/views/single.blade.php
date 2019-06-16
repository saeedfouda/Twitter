@extends('layouts.app')

@section('title', $user->name . ' (@' . $user->username . ') | Twitter')

@section('styles')
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
    <script src="{{ asset('js/single.js') }}" defer></script>
@endsection

@section('content')
    @include('layouts.navbar')
    <!-- Header -->
    <div class="header" id="{{$user->id}}">
        <div class="cover" id="coverPhoto" style="background: {{$user->cover ? 'url(\' ' . asset('/storage/public/covers/' . $user->cover) . ' \')': '#1DA1F2'}}">
            @if($user->id === Auth::user()->id)
                <i class="fa fa-camera" title="Update your cover photo" id='dropdownMenuCover' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'></i>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuCover">
                    <a class="dropdown-item" @click.prevent="pushUploader('coverFile')">Upload cover</a>
                    <a class="dropdown-item" @click.prevent="removePic('removecover', 'coverPhoto')">Remove cover</a>
                    <a class="dropdown-item">Cancel</a>
                </div>
                <input type="file" ref="cover" id="coverFile" class="d-none" @change="uploadHandler('profilecover', 'coverPhoto', 'covers', 'cover')">
            @endif
        </div>
        <div class="user">
            <div class="container">
                @if(Auth::user()->id === $user->id)
                    <div class="profile-pic" {{!is_null($user->photo) ? 'style=background:url(\'' . asset('storage/public/photos/' . $user->photo) . '\')' : ''}} id='dropdownMenuPhoto' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' title="Update your profile picture">
                        @if(is_null($user->photo))
                            <!-- Dropdown of actions -->
                            <i class="fa fa-camera"></i>
                        @endif
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuPhoto">
                                <a class="dropdown-item" @click.prevent="pushUploader('profilePicture')">Upload photo</a>
                                <a class="dropdown-item" @click.prevent="removePic('removepicture', 'profile-pic')">Remove cover</a>
                                <a class="dropdown-item">Cancel</a>
                            </div>
                            <!-- Dropdown of actions -->
                    </div>
                    <input type="file" ref="photo" id="profilePicture" class="d-none" @change="uploadHandler('profilepicture', 'dropdownMenuPhoto', 'photos', 'photo')">
                @else
                    <div class="profile-pic" {{is_null($user->photo) ? "id=dropdownMenuPhoto data-toggle=dropdown aria-haspopup=true aria-expanded=false": 'style=background:url(\'' . asset('storage/public/photos/' . $user->photo) . '\')'}}></div>
                @endif
                <ul class="stats">
                    <li class="active d-xs-none d-sm-none d-md-inline-block">
                        <span class="title">Tweets</span>
                        <span class="value">{{$user->tweets()->count()}}</span>
                    </li>
                    <li class="d-xs-none d-sm-none d-md-inline-block">
                        <span class="title">Following</span>
                        <span class="value">{{$user->following()->count()}}</span>
                    </li>
                    <li class="d-xs-none d-sm-none d-md-inline-block">
                        <span class="title">Followers</span>
                        <span class="value">{{$user->followers()->count()}}</span>
                    </li>
                    <li class="d-xs-block d-sm-block d-md-inline text-right">
                        @if($user->id === Auth::user()->id)
                            <button class="btn edit-profile">Edit Profile</button>
                        @else
                            <button class="btn edit-profile {{$user->followers->contains(Auth::user()->id) ? 'unfollow' : 'follow'}}" @click="follow({{$user->id}}, $event)">{{$user->followers->contains(Auth::user()->id) ? 'Unfollow' : 'Follow'}}</button>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- Header -->

    <!-- Content -->
    <div class="profile">
        <div class="container">
            <div class="row">
                <!-- Left Bar -->
                <div class="d-xs-none d-sm-none d-md-block col-md-3 col-lg-3 about">
                    <div class="name">{{$user->name}}</div>
                    <span class="username" id="username">{{$user->username}}</span>

                    <!-- Creation Date -->
                    <div class="join">
                        <i class="fa fa-calendar-alt"></i>
                        <span>Joined {{$user->created_at->format('F Y')}}</span>
                    </div><!-- Creation Date -->

                </div><!-- Left Bar -->
                <!-- Tweets -->
                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-6 content">
                    <!-- Published Posts -->
                    <div class="posts" id="{{$tweet->id}}">
                        <!-- Single Post -->
                        <div class="post">
                            @if($tweet->user_id === Auth::user()->id)
                                <!-- Dropdown of actions -->
                                    <i class="fa fa-angle-down" id="dropdownMenuI" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuI">
                                        <a class="dropdown-item" @click="deleteTweet({{$tweet->id}}, $event)">Delete Tweet</a>
                                    </div>
                                <!-- Dropdown of actions -->
                            @endif
                            <div class="postContent">
                                <a href="{{route('user.show', $tweet->user->username)}}" class="user">
                                    <img src="{{$tweet->user->photo ? asset('/storage/public/photos/' . $tweet->user->photo) : env('DEFAULT_PHOTO')}}">
                                </a>
                                <div class="data">
                                    <a href="{{route('user.show', $tweet->user->username)}}" class="name">{{$tweet->user->name}}</a>
                                    <a href="{{route('user.show', $tweet->user->username)}}" class="username">{{$tweet->user->username}}</a>
                                    <a href="{{route('tweet.show', ['username' => $tweet->user->username, 'id' => $tweet->id])}}" class="date">{{$tweet->created_at->format('M d')}}</a>
                                </div>
                                <div class="content">
                                    <p>{{$tweet->body}}</p>
                                </div>
                                <div class="actions">
                                    <a href="{{route('tweet.show', ['username' => $tweet->user->username, 'id' => $tweet->id])}}" class="action numberofcomments">
                                        <i class="fa fa-comments"></i>
                                        <span>{{$tweet->comments()->count()}}</span>
                                    </a>
                                    <span class="action{{$tweet->likes->contains(Auth::user()->id) ? ' liked': ''}}" @click.prevent="like({{$tweet->id}}, $event)">
                                        <i class="fa fa-heart"></i>
                                        <span>{{$tweet->likes()->count()}}</span>
                                    </span>
                                    <span class="action">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                </div>
                            </div>
                        </div> <!-- Single Post -->

                        @if($tweet->comments()->orderBy('id', 'desc')->paginate(6)->hasMorePages())
                            <a href="#" class="text text-info" @click.prevent="loadMoreComments">Load more comments</a>
                        @endif

                        <!-- Comment -->
                        <div class="comment" v-for="comment in comments" :id="comment.id">
                            <div class="photo">
                                <a :href="'{{route('user.show', '/')}}/' + comment.user.username">
                                    <img :src="(comment.user.photo) ? '{{asset('/storage/public/photos')}}' + '/' + comment.user.photo: 'https://abs.twimg.com/sticky/default_profile_images/default_profile_normal.png'">
                                </a>
                            </div>
                            <a :href="'{{route('user.show', '/')}}/' + comment.user.username" class="name" v-text="comment.user.name"></a>
                            <a :href="'{{route('user.show', '/')}}/' + comment.user.username" class="username" v-text="comment.user.username"></a>
                            <p v-text="comment.body"></p>
                        </div><!-- Comment -->

                        <!-- New Post -->
                        <div class="publish">
                            <div class="photo">
                                <img src="{{ !is_null(Auth::user()->photo) ? asset('/storage/public/photos/' . Auth::user()->photo) : env('DEFAULT_PHOTO')}}">
                            </div>
                            <textarea type="text" placeholder="What do you think?" maxlength="280" :class='{"has-data": (comment.length > 0)}' v-model="comment"></textarea>
                            <span class="counter" v-text="counter"></span>
                            <button class="btn" :disabled="!canComment || disabled" @click="publish">Comment</button>
                        </div><!-- New Post -->

                    </div><!-- Published Posts -->
                </div><!-- Tweets -->
            </div>
        </div>
    </div><!-- Content -->
@endsection
