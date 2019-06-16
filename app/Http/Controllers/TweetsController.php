<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Tweet as TweetResource;
use App\Tweet;
use App\User;
use App\Events\NewTweet;
use App\Events\NewLike;

class TweetsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $followed   = Auth::user()->following()->pluck('leader_id');
        $followed[] = Auth::user()->id;
        return Tweet::whereIn('user_id', $followed)->orderBy('id', 'DESC')->take(30)->with('user', 'comments', 'likes')->get();
    }

    /**
     * Display an extra listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function moreTweets(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|exists:tweets,id'
        ]);
        $followed   = Auth::user()->following()->pluck('leader_id');
        $followed[] = Auth::user()->id;
        return Tweet::whereIn('user_id', $followed)->where('id', '<', $request->input('id'))->orderBy('id', 'DESC')->take(30)->with('user', 'comments', 'likes')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'tweet' => 'required|string|max:280'
        ]);

        $tweet = new Tweet;
        $tweet->user_id = Auth::user()->id;
        $tweet->body = $request->input('tweet');
        $tweet->save();
        $tweet = $tweet->with('user', 'comments', 'likes')->find($tweet->id);
        $tweet['liked'] = $tweet->likes->contains(Auth::id());

        return $tweet;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $username
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($username, $id)
    {
        $user = User::where('username', $username)->firstOrFail();
        $tweet = $user->tweets()->findOrFail($id);
        return view('single', compact('user', 'tweet'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tweet = Tweet::findOrFail($id);
        if($tweet->user_id === Auth::id()){
            $tweet->delete();
            return response()->json([
                'status' => 'success'
            ], 200);
        }
    }

    /**
     * Set a like on a tweet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function like($id)
    {
        $tweet = Tweet::findOrFail($id);
        if($tweet->likes()->sync(Auth::user()->id, false)){
            // if I'm not the author of the tweet send me an id
            if(Auth::id() !== $tweet->user_id){
                event(New NewLike($tweet));
            }
            return response()->json([
                'status' => 'success'
            ], 200);
        }
    }

    /**
     * remove a like from a tweet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unlike($id)
    {
        $tweet = Tweet::findOrFail($id);
        if($tweet->likes()->detach(Auth::user()->id)){
            // remove any notification related to this
            $notification = new \App\Notification;
            $notification->where('tweet_id', $tweet->id)->where('type', 1)->delete();

            // return response
            return response()->json([
                'status' => 'success'
            ], 200);
        }
    }

}
