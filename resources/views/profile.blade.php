@extends('layouts.app')

@section('title', $user->name . ' (@' . $user->username . ') | Twitter')

@section('styles')
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
    <script src="{{ asset('js/profile.js') }}" defer></script>
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
                                <a class="dropdown-item" @click.prevent="removePic('removepicture', 'dropdownMenuPhoto')">Remove photo</a>
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
                            <a href="{{route('settings.edit')}}">
                                <button class="btn edit-profile">Edit Profile</button>
                            </a>
                        @else
                            <button class="btn edit-profile {{$user->followers->contains(Auth::user()->id) ? 'unfollow' : 'follow'}}" @click="follow({{$user->id}}, $event)">{{$user->followers->contains(Auth::user()->id) ? 'Unfollow' : 'Follow'}}</button>
                            <a href="{{route('messages.show', $user->username)}}" class="btn send-message">Message</a>
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
                    <span class="username">{{$user->username}}</span>

                    @if(!is_null($user->bio))
                        <!-- Bio -->
                        <div class="bio">
                            <p>{{$user->bio}}</p>
                        </div><!-- Bio -->
                    @endif

                    @if(!is_null($user->location))
                        <!-- Location -->
                        <div class="location">
                            <i class="fa fa-map-marker-alt"></i>
                            <span>From <strong>{{$user->location}}</strong></span>
                        </div><!-- Location -->
                    @endif

                    <!-- Creation Date -->
                    <div class="join">
                        <i class="fa fa-calendar-alt"></i>
                        <span>Joined {{$user->created_at->format('F Y')}}</span>
                    </div><!-- Creation Date -->

                </div><!-- Left Bar -->

                <!-- Tweets -->
                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-6 content">
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
                        @if($user->id !== Auth::user()->id)
                            <h3>
                                <span>{{$user->username}}</span>
                                has no Tweets yet.
                            </h3>
                            <p>When they do, their Tweets will show up here.</p>
                        @else
                            <h3>
                                You have no Tweets yet.
                            </h3>
                            <p>When you do, your Tweets will show up here.</p>
                        @endif
                    </div>
                </div><!-- Tweets -->

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
                </div><!-- Right Bar -->
            </div>
        </div>
    </div><!-- Content -->
@endsection
