<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Comment as CommentResource;
use App\Tweet;
use App\Comment;
use App\Events\NewComment;

class CommentsController extends Controller
{
    /**
     * Display a listing of a tweet comments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $tweet = Tweet::findOrFail($id);
        return $tweet->comments()->orderBy('id', 'DESC')->take(6)->with('user')->get();
    }

    /**
    * Display more of a tweet comments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function moreComments(Request $request)
    {
        $this->validate($request, [
            'tweet_id' => 'required|integer|exists:tweets,id',
            'comment_id' => 'required|integer|exists:comments,id'
        ]);
        $tweet = Tweet::findOrFail($request->input('tweet_id'));
        $comments = $tweet->comments()->where('id', '<', $request->input('comment_id'))->orderBy('id', 'desc')->take(6);
        return $comments->with('user')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $this->validate($request, [
            'comment' => 'required|string|max:280'
        ]);
        $tweet = Tweet::findOrFail($id);
        $comment = new Comment;
        $comment->user_id = Auth::user()->id;
        $comment->tweet_id = $tweet->id;
        $comment->body = $request->input('comment');
        $comment->save();

        event(new NewComment($comment));
        return $comment->with('user')->find($comment->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // I have no intention to add this feature right now. But I'm leaving this function so if I decided to add this feature.
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // The same comment as update function :'D
    }

}
