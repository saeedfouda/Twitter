@extends('layouts.app')

@section('title', 'Your messages - Twitter')

@section('styles')
    <link href="{{ asset('css/messenger.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="container">
        <div class="row">
            <div class="messengers col-lg-8 offset-lg-2" v-if="Object.keys(messages).length > 0">
                <a v-for="message in messages" class="single" :id="message.id" :href="'{{route('messages.show', '/')}}/' + message.sender.username">
                    <img :src="'{{asset('/storage/public/photos/')}}/' + message.sender.photo">
                    <span class="name" v-text="message.sender.name"></span>
                    <p class="message" v-text="message.body"></p>
                </a>
            </div>
            <div class="messengers col-lg-8 offset-lg-2" v-else>
                <a class="single">
                    <p class='lead mt-3'>You've no unread messages. if someone sent you a message will appear here.</p>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            const app = new Vue({
                el: '#app',
                data: function(){
                    return {
                        messages: {!! $messages->toJson() !!},
                        canloadmore: true
                    };
                },
                methods: {
                    loadMore: function(){
                        if(this.canloadmore){
                            var lastId = $('.single:last-of-type').attr('id');
                            axios.get(APP_URL + '/moreUnread/' + lastId).then(resp => {
                                if(resp.data.data.length === 0){
                                    this.canloadmore = false;
                                }else{
                                    this.messages = this.messages.concat(resp.data.data);
                                }
                            }).catch(error => {
                                console.log('Error');
                            })
                        }
                    }
                },
                mounted: function(){
                    $('.loading-screen').remove();
                    var overHere = this;
                    $(window).scroll(function() {
                        if($(window).scrollTop() == $(document).height() - $(window).height()) {
                            overHere.loadMore();
                        }
                    });
                }
            });
        });
    </script>
@endsection
