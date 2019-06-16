$(document).ready(function(){
    var USER_ID = $('.header').attr('id');
    var TWEET_ID = $(".posts").attr("id");
    const app = new Vue({
        el: '#app',
        data: function(){
            return {
                file: '',
                comment: '',
                disabled: false,
                comments: [],
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
                        $("#" + elementId).html('').css({"background": "url('" + source_url + "')"});
                    }
                }).catch(error => {
                    showAlert('Sorry we couldn\'t upload your picture. check if you are uploading a valid image and try again. <small>images must be only jpg, or png</small>');
                });
            },
            removePic: function(to, elementId){
                axios.post(APP_URL + '/' + to).then(resp => {
                    if(resp.data.status === 'success'){
                        $('#' + elementId).css({"background": "#1DA1F2"})
                    }
                }).catch(error => {
                    console.log('error: ', error);
                })
            },
            /*~~~~~~~~~~~~~~~~~~* TWEETS *~~~~~~~~~~~~~~~~~~*/
            deleteTweet: function(id, $event){
                var tweet = $($event.target);
                alertify.confirm('Confirm deletion.', 'Are you sure do you want to delete this tweet?', function(){
                    axios.delete(APP_URL + '/delete/' + id).then(resp => {
                        if(resp.data.status === 'success'){
                            // Redirect to home
                            location.href = APP_URL + '/home';
                        }
                    }).catch(error => {
                        // Alert an error
                        showAlert('Sorry we couldn\'t delete your tweet. please refresh the page and try again.');
                    });
                }, function(){
                    // Do nothing
                }).set('labels', {
                    ok: 'Yes. Delete it.',
                    cancel: 'No. Cancel.'
                });
            },
            like: function(id, $event){
                // Action button
                var likeBtn = $($event.target).closest('.action');

                if(likeBtn.hasClass('liked')) { // If tweet is liked

                    // Change the button
                    likeBtn.removeClass('liked').find('span').text(parseInt(likeBtn.find('span').text())-1);
                    // Send request to unlike the tweet
                    axios.post(APP_URL + '/unlike/' + id).then(resp => {
                        if(resp.data.status !== 'success'){
                            // Reset the button
                            likeBtn.addClass('liked').find('span').text(parseInt(likeBtn.find('span').text())+1);
                            // Alert an error
                            showAlert('Sorry we couldn\'t continue your request to unlike this tweet. Refresh the page and try again.');
                        }
                    }).catch(error => {
                        // Reset the button
                        likeBtn.addClass('liked').find('span').text(parseInt(likeBtn.find('span').text())+1);
                        // Alert an error
                        showAlert('Sorry we couldn\'t continue your request to unlike this tweet. Refresh the page and try again.');
                    });
                }else{ // If not liked

                    // Change the button
                    likeBtn.addClass('liked').find('span').text(parseInt(likeBtn.find('span').text())+1);

                    // Send request to like the tweet
                    axios.post(APP_URL + '/like/' + id).then(resp => {
                        if(resp.data.status !== 'success'){
                            // Reset the button
                            likeBtn.removeClass('liked').find('span').text(parseInt(likeBtn.find('span').text())-1);
                            // Alert an error
                            showAlert('Sorry we couldn\'t continue your request to like this tweet. Refresh the page and try again.');
                        }
                    }).catch(error => {
                        // Reset the button
                        likeBtn.removeClass('liked').find('span').text(parseInt(likeBtn.find('span').text())-1);
                        // Alert an error
                        showAlert('Sorry we couldn\'t continue your request to like this tweet. Refresh the page and try again.');
                    });
                }

            },

            /*~~~~~~~~~~~~~~~~~~* COMMENTS *~~~~~~~~~~~~~~~~~~*/
            getComments: function(){
                // Request
                axios.get(APP_URL + '/comments/' + TWEET_ID).then(resp => {
                    // Add response tweets
                    this.comments = resp.data.reverse();
                    var overHere = this;

                    // Remove loading screen
                    $(".loading-screen").remove();

                }).catch(error => {
                    // Alert an error message
                    showAlert('Sorry we couldn\'t fetch comments for this tweet. refresh the page and try again.');
                });
            },
            loadMoreComments: function($event){
                // First shown comment
                var commentId = $(".comment").attr('id');

                // Send the request
                axios.post(APP_URL + '/moreComment', {
                    tweet_id: TWEET_ID,
                    comment_id: commentId
                }).then(resp => {
                    if(resp.data.length == 0){
                        $event.target.remove();
                    }else{
                        // Append results comments
                        this.comments = resp.data.reverse().concat(this.comments);
                    }
                }).catch(error => {
                    // Alert an error
                    showAlert('Sorry we couldn\'t fetch old tweets. refresh the page and try again.');
                });
            },
            // publish a new comment
            publish: function(){
                this.disabled = true;
                axios.post(APP_URL + '/comment/' + TWEET_ID, {
                    'comment': this.comment
                }).then(resp => {
                    this.comment = '';
                    // this.comments = this.comments.concat(resp.data);
                    $('.numberofcomments span').text(parseInt($('.numberofcomments span').text())+1);
                    this.disabled = false;
                    showAlert('Your comment has been published successfully.');
                }).catch(error => {
                    showAlert('Sorry we couldn\'t publish your comment. refresh the page and try again');
                    this.disabled = false;
                });
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
            dateFormat: function(date){
                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                var d = new Date(date);
                var curr_date = d.getDate();
                var curr_month = monthNames[d.getMonth()]
                return curr_month + ' ' + curr_date;
            },
            listen(){
                window.Echo.private('Tweet.' + TWEET_ID)
                .listen('NewComment', (comment) => {
                    this.comments = this.comments.concat(comment);
                });
            }
        },
        mounted: function(){
            this.getComments();
            this.listen();
        }
    });
});
