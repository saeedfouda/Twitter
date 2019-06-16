@extends('layouts.app')

@section('title', $user->name . ' messages - Twitter')

@section('styles')
    <link href="{{ asset('css/messenger.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="messenger">
        <div class="container">
            <div class="row">

                <div class="col-lg-8 offset-lg-2 chat-wrapper">
                    <div class="chat">
                        <div class="name">
                            <a :href="'{{route('user.show', '/')}}/' + partner.username" v-text="partner.name"></a>
                        </div>

                        <div class="messages" id="MessagesWrapper">
                            <div class="message" v-for="(message, key) in messages" :class='{"mine": (message.sender.id === {{Auth::id()}})}' :id="message.id">
                                <img :src="'{{asset('/storage/public/photos')}}/' + message.sender.photo">
                                <ul>
                                    <li v-html="message.body"></li>
                                    <span class="text seen" v-if="(message.seen === 1) && (messages.length === key+1) && (message.sender.id === {{Auth::id()}})">Seen</span>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="write">
                                <textarea class="form-control" id="input" v-model="newMsg"></textarea>
                                <i class="fa fa-paper-plane sendMsg" @click="sendMsg" :disabled="disabled"></i>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery.nicescroll.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            const app = new Vue({
                el: '#app',
                data: function(){
                    return {
                        partner: {!!$user->toJson()!!},
                        messages: {!!$messages->toJson()!!}.reverse(),
                        newMsg: '',
                        disabled: false,
                        // loadMore: true,
                        // contacts: ,
                        // partner: null,
                    };
                },
                methods: {
                    sendMsg: function(){
                        if(this.newMsg){
                            this.disabled = true;
                            axios.post(APP_URL + '/newmessage', {
                                receiver_id: this.partner.id,
                                message: this.newMsg
                            }).then(resp => {
                                this.newMsg = '';
                                this.messages.push(resp.data);
                                this.disabled = false;
                                setTimeout(function(){
                                    // Scroll to bottom
                                    var chatWindow = document.getElementsByClassName("messages")[0];
                                    chatWindow.scrollTop = chatWindow.scrollHeight;
                                }, 100);
                            }).catch(error => {
                                // Fire an alert
                                showAlert('Sorry we could\'nt send your message. refresh the page and try again');
                                this.disabled = false;
                            });
                        }
                    },
                    listen: function(){
                        var channelName = 'Chat.' + this.partner.id;
                        window.Echo.private(channelName)
                        .on("App\\Events\\NewMessage", (message) => {
                            this.messages.push(message.message);
                            setTimeout(function(){
                                // Scroll to bottom
                                var chatWindow = document.getElementsByClassName("messages")[0];
                                chatWindow.scrollTop = chatWindow.scrollHeight;

                                // Send a request that I've seen the message
                                axios.post(APP_URL + '/IveSeenTheMessage/' + {{$user->id}}).then(resp => {
                                    // Do nothing
                                });
                            }, 100);
                        });
                        window.Echo.private('Chat.' + this.partner.id)
                        .listen('SeenMessages', (seen) => {
                            if(seen.status === 'seen'){
                                var lastMsg = this.messages[this.messages.length - 1];
                                if(lastMsg.sender.id === {{Auth::id()}}){
                                    lastMsg.seen = 1;
                                }
                            }
                        });
                    }
                },
                mounted: function(){
                    this.listen();
                    // Scroll to bottom
                    var chatWindow = document.getElementsByClassName("messages")[0];
                    chatWindow.scrollTop = chatWindow.scrollHeight;
                    $('#MessagesWrapper').niceScroll({
                        cursorcolor: "#3095dc",
                    });
                    $('#input').niceScroll({
                        cursorcolor: "#3095dc",
                    });
                    $('.loading-screen').remove();
                }
            });
        });
    </script>
@endsection
