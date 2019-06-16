@extends('layouts.app')

@section('title', 'Someone - Twitter Search')

@section('styles')
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="search">
        <div class="container">
            <div class="row" v-if="guys.length > 0">
                <div class="col-md-6 col-lg-3 ppl" v-for="(guy, key) in guys" :id="guy.id">
                    <!-- Profile Cards -->
                    <div class="profileCard">
                        <a :style="(guy.cover) ? 'background: url({{asset("/storage/public/covers/' + guy.cover + '")}})' : ''" :href="'{{route('user.show', '/')}}/' + guy.username" class="cover"></a>
                        <div class="profileContent">
                            <a :href="'{{route('user.show', '/')}}/' + guy.username">
                                <div class="avatar" :style="(guy.photo) ? 'background: url({{asset("/storage/public/photos/' + guy.photo + '" )}})' : 'background: url(https://abs.twimg.com/sticky/default_profile_images/default_profile_normal.png)'"></div>
                                <div class="userDetails">
                                    <span class="name" v-text="guy.name"></span>
                                    <span class="username" v-text="guy.username"></span>
                                </div>
                            </a>
                            <div class="userStats row row-eq-height">
                                <div class="col-sm-6">
                                    <span class="mini-title">Tweets<span>
                                    <span class="value" v-text="guy.tweets.length"></span>
                                </div>
                                <div class="col-sm-6">
                                    <button class="follow" :class='{"followed": isFollowed(key)}' @click="follow(key, $event)" :disabled="isFollowed(key)">
                                        <span v-if="!isFollowed(key)">Follow</span>
                                        <span v-if="isFollowed(key)">Followed</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Profile Cards -->
                </div>
            </div>
            <div class="col-sm-12 text-center mt-5" v-else>
                <h1 dir="ltr">No results found with your search. make sure thay you write it right وكدا يعني</h1>
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
                        guys: {!!$results->toJson()!!},
                        moreScroll: true
                    };
                },
                mounted: function(){
                    $('.loading-screen').remove();
                    // Fire an event to load ajax requests on scroll to bottom
                    var overHere = this;
                    if(this.guys.length >= 28){
                        $(window).scroll(function() {
                            if($(window).scrollTop() == $(document).height() - $(window).height()) {
                                overHere.loadMoreResearches();
                            }
                        });
                    }
                },
                methods: {
                    follow: function(key, $event){
                        if(!this.isFollowed(key)){
                            // Push me to the followers
                            this.guys[key].followers.push({id: AUTH_ID});
                            axios.post(APP_URL + '/follow/' + this.guys[key].id).then(resp => {
                                if(resp.data.status !== 'success'){
                                    // It was a fake alert. remove me from the followers again. Sorry dude
                                    for(var follower in this.guys[key].followers){
                                        if(this.guys[key].followers[follower].id == AUTH_ID){
                                            this.guys.slice(key, 1);
                                        }
                                    }
                                    // Alert an error
                                    showAlert('Sorry we couldn\'t execute your action.');
                                }
                            }).catch(error => {
                                showAlert('Sorry we couldn\'t continue your request to follow this profile.');
                            });
                        }
                    },
                    isFollowed: function(key){
                        var followers = this.guys[key].followers;
                        for(var follower in followers){
                            if(followers[follower].id == AUTH_ID){
                                return true;
                            }
                        }
                    },
                    loadMoreResearches: function(){
                        if(this.moreScroll){
                            var lastId = $('.search .ppl:last-of-type').attr('id');
                            // Send the request
                            axios.post(APP_URL + '/moreresults', {
                                id: lastId
                            }).then(resp => {
                                // Append results tweets
                                this.guys = this.guys.concat(resp.data);
                                if(resp.data.length == 0){
                                    this.moreScroll = false;
                                }
                            }).catch(error => {
                                // Alert an error
                                showAlert('Sorry we couldn\'t fetch old tweets. refresh the page and try again.');
                            });
                        }
                    }
                }
            });
        });
    </script>
@endsection
