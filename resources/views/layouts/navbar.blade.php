<nav class="navbar fixed-top">
    <div class="container">
        <ul>
            <a href="{{url('/')}}">
                <li class="{{Request::is('home') ? 'active' : ''}}">
                    <i class="{{Request::is('home') ? 'fa fa-home' : 'fab fa-twitter'}}"></i>
                    <span class="d-lg-inline-block d-xs-none d-sm-none">Home</span>
                </li>
            </a>
            @guest
                <a href="{{route('about')}}" class="d-lg-inline-block d-xs-none d-sm-none">
                    <li>
                        <span>About</span>
                    </li>
                </a>
            @endguest

            @auth

                <!-- Notifications -->
                <a href="{{route('notifications')}}">
                    <li class="{{Request::is('notifications*') ? 'active' : ''}}">
                        <i class="fa fa-bell"></i>
                        <span class="d-lg-inline-block d-xs-none d-sm-none">Notifications</span>
                        @if(\App\Notification::where('user_id', Auth::id())->where('seen', '0')->count())
                            <i class="new-notify"></i>
                        @endif
                    </li>
                </a><!-- Notifications -->

                <!-- Messages -->
                <a href="{{route('messages.index')}}">
                    <li class="{{Request::is('messages*') ? 'active' : ''}}">
                        <i class="fa fa-envelope"></i>
                        <span class="d-lg-inline-block d-xs-none d-sm-none">Messages</span>
                        @if(\App\Message::where('receiver_id', Auth::id())->where('seen', '0')->count())
                            <i class="new-notify"></i>
                        @endif
                    </li>
                </a><!-- Messages -->



            @endauth
        </ul>

        <a href="{{ url('/') }}"> <span class="visuallyhidden" style="display: inline-block; width: 24px; height: 21px;"  >
            <img src="{{ asset('images/bird.svg') }}"  alt="Twitter">
           </span>
            </a>

        @auth
            <div class="form-inline my-2 my-lg-0 navbar-form d-xs-none d-sm-none d-md-flex">
                <form action="{{url('/search/')}}" method="GET">
                    <div class="input-group">
                        <input class="form-control mr-sm-2" type="search" placeholder="Search Twitter" name="search" value="{{Request::get('search')}}">
                        <div class="input-group-btn">
                            <button class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <div class="dropdown">
                    <img id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" src="{{ !is_null(Auth::user()->photo) ? asset('/storage/public/photos/' . Auth::user()->photo) : env('DEFAULT_PHOTO')}}">
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{route('user.show', Auth::user()->username)}}">Profile</a>
                        <a class="dropdown-item" href="{{route('settings.edit')}}">Settings</a>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault; $('#logout').submit();">Logout</a>
                    </div>
                    <form action="{{route('logout')}}" method="POST" class="d-none" id="logout">
                        {{csrf_field()}}
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
