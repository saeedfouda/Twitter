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
                        <div class="title">Password</div>
                        <!-- Form -->
                        <div class="form">

                            <!-- Current Password -->
                            <div class="row">
                                <label for="current_password" class="col-xs-4 col-sm-2">Current password</label>
                                <div class="form-group col-xs-8 col-sm-8">
                                    <input type="password" v-validate="'required|min:6'" name="current_password" class="form-control" placeholder="Enter your current password." v-model="current_password">
                                    <a class="text text-danger" v-text="errors.first('current_password') || firstElement(error.current_password)"></a>
                                </div>
                            </div><!-- Current Password -->

                            <!-- Current Password -->
                            <div class="row">
                                <label for="new_password" class="col-xs-4 col-sm-2">New password</label>
                                <div class="form-group col-xs-8 col-sm-8">
                                    <input type="password" class="form-control" v-validate="'required|min:6|confirmed:password_confirmation'" name="new_password" id="new_password" placeholder="Enter your new password." v-model="new_password">
                                    <a class="text text-danger" v-text="errors.first('new_password') || firstElement(error.new_password)"></a>
                                </div>
                            </div><!-- Current Password -->

                            <!-- Confirm new password -->
                            <div class="row">
                                <label for="password_confirmation" class="col-xs-4 col-sm-2">Password confirmation</label>
                                <div class="form-group col-xs-8 col-sm-8">
                                    <input type="password" class="form-control" ref="password_confirmation" v-validate="'required'" name="password_confirmation" id="password_confirmation" placeholder="Confirm your new password." v-model="password_confirmation">
                                    <a class="text text-danger" v-text="errors.first('password_confirmation') || firstElement(error.password_confirmation)"></a>
                                </div>
                            </div><!-- Confirm new password -->

                            <div class="form-group offset-sm-4 offset-md-2" style="padding-left: 15px;">
                                <input type="submit" class="btn btn-primary" style="color: #FFF;" @click="updatePassword" :disabled="errors.any()">
                            </div>

                        </div><!-- Form -->
                    </div><!-- Section -->

                </div>
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
                        current_password: '',
                        new_password: '',
                        password_confirmation: '',
                        error: {}
                    };
                },
                methods: {
                    updatePassword: function(){
                        axios.post(APP_URL + '/settings/safety', {
                            current_password: this.current_password,
                            new_password: this.new_password,
                            new_password_confirmation: this.password_confirmation
                        }).then(resp => {
                            if(resp.data.status === 'success'){
                                showAlert('Password has been successfully changed.');
                            }else{
                                // Fire an alert
                                showAlert('Sorry we couldn\'t complete your request to update the password. Refresh the page and try again.');
                            }
                        }).catch(error => {
                            this.error = error.response.data.errors;
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
                },
                watch: {
                    current_password: function(){
                        this.error.current_password = '';
                    },
                    new_password: function(){
                        this.error.new_password = '';
                    },
                    password_confirmation: function(){
                        this.error.password_confirmation = '';
                    }
                }
            });
        });
    </script>
@endsection
