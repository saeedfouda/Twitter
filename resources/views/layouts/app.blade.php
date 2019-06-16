@include('layouts.header')
    <div id="app">
        <div class="loading-screen"><img src="{{asset('images/bird.svg')}}" alt="Page is loading" title="Please wait. page is loading"></div>
        @yield('content')
    </div>
@include('layouts.footer')
