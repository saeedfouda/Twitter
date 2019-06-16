<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Message as MessageResource;
use App\Http\Resources\User as UserResource;

class MessagesController extends Controller
{
    public function index($id = null)
    {
        $messages = Message::where('receiver_id', Auth::id())->where('seen', 0)->orderBy('id', 'DESC')->take(25)->get()->unique('sender_id');
        $messages = MessageResource::collection($messages);
        return view('messages', compact('messages'));
    }

    public function moreUnread(int $id)
    {
        $messages = Message::where('id', '<', $id)->where('receiver_id', Auth::id())->where('seen', 0)->orderBy('id', 'DESC')->take(25)->get()->unique('sender_id');
        return MessageResource::collection($messages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $this->validate($request, [
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required|string'
        ]);

        $message = new Message;
        $message->sender_id = Auth::user()->id;
        $message->receiver_id = $request->input('receiver_id');
        $message->body = $request->input('message');
        $message->seen = 0;
        if($message->save()){
            event(new \App\Events\NewMessage($message));
            return response()->json(new MessageResource($message->with('sender', 'receiver')->find($message->id)), 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function partner($id)
    {
        $partner = User::findOrFail($id);
        return $partner;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $username
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {

        // Check the username
        $user = User::where('username', $username)->firstOrFail();

        // Make messages seen
        Message::where([
            ['sender_id', '=', $user->id],
            ['receiver_id', '=', Auth::id()]
            ])->update(['seen' => 1]);
            
        // Push a notification so if he's online
        event(New \App\Events\SeenMessages($user->id));

        // get messages
        $messages = Message::where(function($query) use ($user){
            $query->where('sender_id', $user->id)->where('receiver_id', Auth::id());
        })->orWhere(function($query) use ($user){
            $query->where('sender_id', Auth::id())->where('receiver_id', $user->id);
        })->orderBy('id', 'DESC')->take(30)->get();
        $messages = MessageResource::collection($messages);
        // view conversation
        return view('conversation', compact('messages', 'user'));
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
        //
    }

    public function IveSeenThis($id)
    {
        event(New \App\Events\SeenMessages($id));
    }
}
