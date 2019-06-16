$(document).ready(function(){
    var USER_ID = $('.header').attr('id');
    const app = new Vue({
        el: '#app',
        data: function(){
            return {
                file: '',
                tweets: [],
                moreScroll: true
            };
        },
        computed: {
            counter: function(){
                return this.comment.length ? (280 - this.comment.length) : ''
            },
            canComment: function(){
                return this.comment.length > 0;
            }
        },
        methods: {
            /*~~~~~~~~~~~~~~~~~~* cover & photo update *~~~~~~~~~~~~~~~~~~*/
            pushUploader: function(id){
                $("#" + id).click();
            },
            uploadHandler: function(to, elementId, folder, dynRef){
                this.file = eval('this.$refs.' + dynRef + '.files[0]');

                let formData = new FormData();
                formData.append('image', this.file);

                // Send request
                axios.post(APP_URL + '/' + to, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(resp => {
                    if(resp.data.status === 'success'){
                        let source_url = APP_URL + "/storage/" + folder + '/' + resp.data.image;
                        $("#" + elementId).css({"background": "url('" + source_url + "')"}).find("i").remove();
                    }
                }).catch(error => {
                    showAlert('Sorry we couldn\'t upload your picture. check if you are uploading a valid image and try again. <small>images must be only jpg, or png</small>');
                });
            },
            removePic: function(to, elementId){
                axios.post(APP_URL + '/' + to).then(resp => {
                    if(resp.data.status === 'success'){
                        $('#' + elementId).css({"background": "#1DA1F2"}).append('<i class="fa fa-camera"></i>');
                    }
                }).catch(error => {
                    showAlert('Sorry we couldn\'t upload your picture. check if you are uploading a valid image and try again. <small>images must be only jpg, or png</small>');
                })
            },
            /*~~~~~~~~~~~~~~~~~~* TWEETS *~~~~~~~~~~~~~~~~~~*/
            getTweets: function(){
                // Request
                axios.get(APP_URL + '/profile/' + USER_ID + '/tweets').then(resp => {
                    // Add response tweets
                    this.tweets = resp.data;
                    var overHere = this;

                    // Run an event for ajax load
                    if(this.tweets.length >= 30){
                        $(window).scroll(function() {
                            if($(window).scrollTop() == $(document).height() - $(window).height()) {
                                overHere.loadMoreTweets();
                            }
                        });
                    }

                    // Remove loading screen
                    $(".loading-screen").remove();
                }).catch(error => {
                    // Alert an error message
                    showAlert('Sorry we couldn\'t fetch home tweets. refresh the page and try again.');
                });
            },
            loadMoreTweets: function(){
                if(this.moreScroll){
                    var lastId = $('.posts .post:last-of-type').attr('id');
                    // Send the request
                    axios.post(APP_URL + '/profile/' + USER_ID + '/moreTweets', {
                        id: lastId
                    }).then(resp => {
                        // Append results tweets
                        this.tweets = this.tweets.concat(resp.data);
                        if(resp.data.length == 0){
                            this.moreScroll = false;
                        }
                    }).catch(error => {
                        // Alert an error
                        showAlert('Sorry we couldn\'t fetch old tweets. refresh the page and try again.');
                    });
                }
            },
            deleteTweet: function(key){
                var tweet = this.tweets[key],
                    overHere = this;
                alertify.confirm('Confirm deletion.', 'Are you sure do you want to delete this tweet?', function(){
                    axios.delete(APP_URL + '/delete/' + tweet.id).then(resp => {
                        if(resp.data.status === 'success'){
                            showAlert('Your tweet has been deleted');
                            $('#' + tweet.id).hide('slow', function(){
                                overHere.tweets.slice(key, 1);
                            });
                        }
                    }).catch(error => {
                        showAlert('Sorry we couldn\'t delete your tweet. please refresh the page and try again.');
                    });
                }, function(){
                    // Do nothing
                }).set('labels', {
                    ok: 'Yes. Delete it.',
                    cancel: 'No. Cancel.'
                });
            },
            like: function(key){
                if(this.isLiked(this.tweets[key].likes)) { // If liked
                    for(var like in this.tweets[key].likes) {
                        if(this.tweets[key].likes[like].id == AUTH_ID) {
                            this.tweets[key].likes.splice(like, 1);

                            // Send request to unlike the tweet
                            axios.post(APP_URL + '/unlike/' + this.tweets[key].id).then(resp => {
                                if(resp.data.status !== 'success'){
                                    // Rechange the button on error
                                    this.tweets[key].likes.push({id: AUTH_ID});
                                    showAlert('Sorry we couldn\'t continue your request to unlike this tweet. Refresh the page and try again.');
                                }
                            }).catch(error => {
                                // Alert an error
                                this.tweets[key].likes.push({id: AUTH_ID});
                                showAlert('Sorry we couldn\'t continue your request to unlike this tweet. Refresh the page and try again.');
                            });
                        }
                    }
                }else{ // If not liked

                    // Push me in likers
                    var newliker = {id: AUTH_ID};
                    this.tweets[key].likes.push(newliker);

                    // Send request to like the tweet
                    axios.post(APP_URL + '/like/' + this.tweets[key].id).then(resp => {
                        if(resp.data.status !== 'success'){
                            // Reset the likes
                            this.tweets[key].likes.splice(this.tweets[key].likes.indexOf(newliker), 1);
                            // Alert an error
                            showAlert('Sorry we couldn\'t continue your request to like this tweet. Refresh the page and try again.');
                        }
                    }).catch(error => {
                        // Reset the likes
                        this.tweets[key].likes.splice(this.tweets[key].likes.indexOf(newliker), 1);
                        // Alert an error
                        showAlert('Sorry we couldn\'t continue your request to like this tweet. Refresh the page and try again.');
                    });
                }
            },
            /*~~~~~~~~~~~~~~~~~~* SUGGESTED USERS *~~~~~~~~~~~~~~~~~~*/
            follow: function(id, $event){
                var btn = $($event.target);
                if(btn.hasClass('follow')){ // If not followed
                    // Change the button
                    btn.text('Unfollow').removeClass('follow').addClass('unfollow');
                    // Send request
                    axios.post(APP_URL + '/follow/' + id).then(resp => {
                        if(resp.data.status !== 'success'){
                            // Change the button on error
                            btn.text('Follow').removeClass('unfollow').addClass('follow');
                            showAlert('Sorry we couldn\'t execute your action. refresh the page and try again.');
                        }else{
                            btn.parents('.someone').hide('slow', function(){
                                $(this).remove();
                            })
                        }
                    }).catch(error => {
                        showAlert('Sorry we couldn\'t continue your request to follow this profile. refresh the page and try again.');
                    });
                }else{
                    // Change the button
                    btn.text('Follow').removeClass('unfollow').addClass('follow');
                    // Send request
                    axios.post(APP_URL + '/unfollow/' + id).then(resp => {
                        if(resp.data.status !== 'success'){
                            // Change the button on error
                            btn.text('Unfollow').removeClass('follow').addClass('unfollow');
                            showAlert('Sorry we couldn\'t execute your action. refresh the page and try again.');
                        }else{
                            btn.parents('.someone').hide('slow', function(){
                                $(this).remove();
                            })
                        }
                    }).catch(error => {
                        showAlert('Sorry we couldn\'t continue your request to follow this profile. refresh the page and try again.');
                    });
                }
            },

            /*~~~~~~~~~~~~~~~~~~* *.* *~~~~~~~~~~~~~~~~~~*/
            isLiked: function(likes){
                for(var like in likes){
                    if(likes[like].id == AUTH_ID){
                        return true;
                    }
                }
            },
            dateFormat: function(date){
                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var d = new Date(date);
                var curr_date = d.getDate();
                var curr_month = monthNames[d.getMonth()]
                return curr_month + ' ' + curr_date;
            }
        },
        mounted: function(){
            this.getTweets();
        }
    });
});
