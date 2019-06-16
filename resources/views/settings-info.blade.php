@extends('layouts.app')

@section('title', 'Twitter / Settings')

@section('styles')
    <link href="{{ asset('css/settings.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('layouts.navbar')
    <div class="settings">
        <div class="container">
            <div class="row">
                <div class="d-xs-none d-sm-none d-md-block col-md-5 col-lg-3 left-bar">
                    @include('partials.settings.sidebar')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-9 forms">
                    <!-- Section -->
                    <div class="section">
                        <div class="title">Account</div>
                        <!-- Form -->
                        <div class="form">

                            <!-- Name -->
                            <div class="row">
                                <label for="name" class="col-xs-4 col-sm-2">Name</label>
                                <div class="form-group col-xs-8 col-sm-8">
                                    <input type="text" name="name" v-validate="'required|max:255'" id="name" class="form-control" v-model="user.name">
                                    <a class="text text-danger" v-text="errors.first('name') || firstElement(error['user.name'])"></a>
                                </div>
                            </div><!-- Name -->

                            <!-- Username -->
                            <div class="row">
                                <label for="username" class="col-xs-4 col-sm-2">Username</label>
                                <div class="form-group col-xs-8 col-sm-8">
                                    <input type="text" v-validate="'required|max:255|alpha_dash|unique:username'" data-vv-validate-on="change" name="username" id="username" class="form-control" v-model="user.username">
                                    <a class="text" v-text="'https://twitter.com/' + user.username" v-if="!errors.has('username')"></a>
                                    <a class="text text-danger" v-text="errors.first('username')"></a>
                                </div>
                            </div><!-- Username -->

                            <!-- Email -->
                            <div class="row">
                                <label class="col-xs-4 col-sm-2" for="email">Email</label>
                                <div class="form-group col-xs-8 col-sm-8">
                                    <input type="text" v-validate="'required|email|max:255|unique:email'" data-vv-validate-on="change" name="email" class="form-control" id="email" v-model="user.email" autocomplete="off">
                                    <a class="text" v-if="!errors.has('email')">Email will not be publicly displayed</a>
                                    <a class="text text-danger" v-text="errors.first('email')"></a>
                                </div>
                            </div><!-- Email -->

                            <!-- Language -->
                            <div class="row">
                                <label class="col-xs-4 col-sm-2" for="language">Language</label>
                                <div class="form-group col-xs-8 col-sm-8">
                                    <select name="language" id="language" class="form-control">
                                        <option value="1">English</option>
                                        <option value="2">انجليزي</option>
                                        <option value="3">Anglais</option>
                                    </select>
                                </div>
                            </div><!-- Language -->
                        </div><!-- Form -->
                    </div><!-- Section -->

                    <!-- Section -->
                    <div class="section">
                        <div class="title">Personal information</div>
                        <!-- Form -->
                        <div class="form">

                            <!-- Bio -->
                            <div class="row">
                                <label class="col-xs-4 col-sm-2" for="bio">Bio</label>
                                <div class="form-group col-xs-8 col-sm-8">
                                    <textarea name="bio" v-validate="'max:160'" class="form-control" id="bio" rows="4" style="resize: none" v-model="user.bio" placeholder="A brief about you..."></textarea>
                                    <a class="text text-danger" v-text="errors.first('bio')"></a>
                                </div>
                            </div><!-- Bio -->

                            <!-- Location -->
                            <div class="row">
                                <label class="col-xs-4 col-sm-2" for="location">Location</label>
                                <div class="form-group col-xs-8 col-sm-8">
                                    <input type="text" id="location" class="form-control" name="location" v-model="user.location" placeholder="Where do you live?" v-validate="'max:160'">
                                    <a class="text text-danger" v-text="errors.first('location')"></a>
                                </div>
                            </div><!-- Location -->

                            <div class="form-group offset-sm-4 offset-md-2" style="padding-left: 15px;">
                                <input type="submit" class="btn btn-primary" style="color: #FFF;" @click="saveSettings" :disabled="errors.any()">
                            </div>

                        </div><!-- Form -->
                    </div><!-- Section -->

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script defer>
        $(document).ready(function(){
            const app = new Vue({
                el: '#app',
                data: function(){
                    return {
                        user: {!! Auth::user()->toJson() !!},
                        disabled: false,
                        error: {}
                    };
                },
                methods: {
                    saveSettings: function(){
                        this.disabled = true;
                        axios.post(APP_URL + '/settings/account', {
                            user: this.user
                        }).then(resp => {
                            if(resp.data.status === 'success'){
                                // Fire an alert
                                showAlert('Your personal information have been saved successfully');
                            }else{
                                // Fire an alert
                                showAlert('An error happened while updating your information. Refresh the page and try again.');
                            }
                        }).catch(error => {
                            // Fire an alert
                            showAlert('An error happened while updating your information. Refresh the page and try again.');
                        });
                    },
                    firstElement: function(object){
                        if(typeof object === "object"){
                            return object[0];
                        }
                    }
                },
                mounted: function(){
                    $('.loading-screen').remove();
                }
            });
        });
    </script>
@endsection
