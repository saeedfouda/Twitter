<!-- Profile Cards -->
<div class="profileCard">
    <a href="{{route('user.show', Auth::user()->username)}}" class="cover" style="background: {{ !is_null(Auth::user()->cover) ? 'url(' . asset('/storage/public/covers/' . Auth::user()->cover) . ')' : ''}}"></a>
    <div class="profileContent">
        <div class="avatar" style="background: {{ !is_null(Auth::user()->photo) ? 'url(' . asset('/storage/public/photos/' . Auth::user()->photo) . ')' : '' }}"></div>
        <div class="userDetails">
            <a href="{{route('user.show', Auth::user()->username)}}" class="name">{{Auth::user()->name}}</a>
            <a href="{{route('user.show', Auth::user()->username)}}" class="username">{{Auth::user()->username}}</a>
        </div>
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

<div class="sections">
    <a href="{{route('settings.account')}}" {{Request::is('settings/account*') ? "class=active" : ''}}>Account <i class='fa fa-angle-right'></i></a>
    <a href="{{route('settings.safety')}}" {{Request::is('settings/safety*') ? "class=active" : ''}}>Privacy and safety <i class='fa fa-angle-right'></i></a>
</div>

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
