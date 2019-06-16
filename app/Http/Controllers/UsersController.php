<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Tweet as TweetResource;
use App\User;
use Session;
use Illuminate\Support\Facades\Hash;
use App\Events\NewFollow;

class UsersController extends Controller
{
    /**
     * Display a listing of user tweets
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        return User::FindOrFail($id)->tweets()->orderBy('id', 'DESC')->take(30)->with('user', 'comments', 'likes')->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $username
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {

        $followed   = Auth::user()->following()->pluck('leader_id');
        $followed[] = Auth::user()->id;
        $unfollowed  = User::whereNotIn('id', $followed)->pluck('id');
        $suggestedIds = \DB::table('followers')->whereIn('follower_id', $followed)->whereIn('leader_id', $unfollowed)->pluck('leader_id');
        $suggested = count(User::whereIn('id', $suggestedIds)->take(6)->get()) ? User::whereIn('id', $suggestedIds)->take(6)->get(): User::whereNotIn('id', $followed)->take(6)->get();

        $user = User::where('username', $username)->firstOrFail();
        return view('profile', compact('user', 'suggested'));
    }

    /**
     * Display an extra listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function moreTweets(Request $request, $id)
    {
        $user = User::FindOrFail($id);
        $this->validate($request, [
            'id' => 'required|integer|exists:tweets,id'
        ]);
        $tweets = $user->tweets()->where('id', '<', $request->input('id'))->orderBy('id', 'DESC')->take(30);
        return $tweets->with('user', 'comments', 'likes')->get();
    }

    /**
     * Show the form for editing user data.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        return view('settings-info');
    }

    /**
     * Update the account information in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function accountUpdate(Request $request)
    {

        // Validation

        if(
            // If username and email are the same as before
            $request->input('user.username') === Auth::user()->username &&
            $request->input('user.email') === Auth::user()->email
        ){
            $this->validate($request, [
                'user.name' => 'required|string|max:255',
                'user.bio' => 'nullable|string|max:160',
                'user.location' => 'nullable|string|max:160'
            ]);

        // If username is the same as before
        }elseif($request->input('user.username') === Auth::user()->username) {
            $this->validate($request, [
                'user.name' => 'required|string|max:255',
                'user.email' => 'required|string|email|max:255|unique:users',
                'user.bio' => 'nullable|string|max:160',
                'user.location' => 'nullable|string|max:160'
            ]);

            // If email is the same as before
        }elseif($request->input('user.email') === Auth::user()->email){
            $this->validate($request, [
                'user.name' => 'required|string|max:255',
                'user.username' => 'required|string|max:255|alpha_dash|unique:users,username',
                'user.bio' => 'nullable|string|max:160',
                'user.location' => 'nullable|string|max:160'
            ]);

            // If email and username are different
        }else{
            $this->validate($request, [
                'user.name' => 'required|string|max:255',
                'user.username' => 'required|string|max:255|alpha_dash|unique:users,username',
                'user.email' => 'required|string|email|max:255|unique:users',
                'user.bio' => 'nullable|string|max:160',
                'user.location' => 'nullable|string|max:160'
            ]);

        }

        // Storing in database
        Auth::user()->name = $request->input('user.name');
        Auth::user()->username = $request->input('user.username');
        Auth::user()->email = $request->input('user.email');
        Auth::user()->bio = $request->input('user.bio');
        Auth::user()->location = $request->input('user.location');
        if(Auth::user()->save()){
            return response()->json([
                'status' => 'success'
            ], 200);
        }
    }

    /**
     * Check if the input data is unique in database or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkUnique(Request $request)
    {
        $this->validate($request, [
            'uniqueTo' => 'required|in:email,username'
        ]);
        if($request->input('value') !== Auth::user()[$request->input('uniqueTo')]){
            $this->validate($request, [
                'value' => 'required|unique:users,' . $request->input('uniqueTo')
            ]);
        }
    }

    /**
    * Show the form for editing user data.
    *
    * @return \Illuminate\Http\Response
     */
    public function editPassword(Request $request)
    {
        return view('settings-safety');
    }

    /**
     * Check if the input data is unique in database or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        // Validation
        $this->validate($request, [
            'current_password' => 'required|min:6',
            'new_password' => 'required|min:6|confirmed'
        ]);

        if(Hash::check($request->input('current_password'), Auth::user()->password)){

            // update the password
            Auth::user()->password = Hash::make($request->input('new_password'));
            return Auth::user()->save() ? response()->json(['status' => 'success'], 200): [];

        }else{
            // else If password is incorrect return an error
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => [
                    'current_password' => ['Password incorrect']
                ]
            ], 422);
        }

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

    /**
     * Update user data in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(Request $request)
    {
        //
    }

    /**
     * Follow a user;
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function follow($id)
    {
        $user = User::findOrFail($id);
        if($user->id !== Auth::user()->id){
            if($user->followers()->sync(Auth::user()->id, false)){
                // Send a notification
                event(new NewFollow($user));
                return response()->json([
                    'status' => 'success'
                ], 200);
            }
        }
    }

    /**
     * Unfollow a user;
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unfollow($id)
    {
        $user = User::findOrFail($id);
        if($user->id !== Auth::user()->id){
            if($user->followers()->detach(Auth::user()->id)){
                // Remove the notification
                $notification = new \App\Notification;
                $notification->where('user_id', $user->id)->where('type', 3)->delete();
                return response()->json([
                    'status' => 'success'
                ], 200);
            }
        }
    }

    /**
     * Update profile picture;
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
     public function photo(Request $request)
     {
         $this->validate($request, [
             'image' => 'required|image|mimes:jpg,jpeg,png|max:5000'
         ]);

         if(!is_null(Auth::user()->photo)){
             unlink(storage_path() . '/app/public/photos/' . Auth::user()->photo);
         }
         $ext = $request->file('image')->getClientOriginalExtension();
         $name = md5(uniqid()) . now()->timestamp . '.' . $ext;
         $request->file('image')->storeAs('public/photos', $name);
         Auth::user()->photo = $name;
         if(Auth::user()->save()){
             return response()->json([
                 'status' => 'success',
                 'image' => $name
             ], 200);
         }
     }

     /**
      * Update cover photo;
      *
      * @param  Request  $request
      * @return \Illuminate\Http\Response
      */
      public function cover(Request $request)
      {
          $this->validate($request, [
              'image' => 'required|image|mimes:jpg,jpeg,png|max:5000'
          ]);

          if(!is_null(Auth::user()->cover)){
              unlink(storage_path() . '/app/public/covers/' . Auth::user()->cover);
          }
          $ext = $request->file('image')->getClientOriginalExtension();
          $name = md5(uniqid()) . now()->timestamp . '.' . $ext;
          $request->file('image')->storeAs('public/covers', $name);
          Auth::user()->cover = $name;
          if(Auth::user()->save()){
              return response()->json([
                  'status' => 'success',
                  'image' => $name
              ], 200);
          }
      }

      /**
       * Remove profile photo;
       *
       * @return \Illuminate\Http\Response
       */
       public function removePhoto()
       {
           unlink(storage_path() . '/app/public/photos/' . Auth::user()->photo);
           Auth::user()->photo = null;
           if(Auth::user()->save()){
               return response()->json([
                   'status' => 'success',
               ], 200);
           }
       }

       /**
        * Remove profile photo;
        *
        * @return \Illuminate\Http\Response
        */
        public function removeCover()
        {
            unlink(storage_path() . '/app/public/covers/' . Auth::user()->cover);
            Auth::user()->cover = null;
            if(Auth::user()->save()){
                return response()->json([
                    'status' => 'success',
                ], 200);
            }
        }

        /**
         * Search for a specified resource.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function search(Request $request)
        {
            $value = $request->get('search');
            Session::put('search', $value);
            $results = User::where('name', 'LIKE', "%{$value}%")->orderBy('id', 'DESC')->take(28)->with('tweets', 'followers')->get();
            return view('search', compact('results'));
        }

        /**
         * Get more search results.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function searchMore(Request $request)
        {
            $this->validate($request, [
                'id' => 'required|integer|exists:users,id'
            ]);
            $value = Session::get('search');
            $results = User::where('name', 'LIKE', "%{$value}%")->where('id', '<', $request->input('id'))->orderBy('id', 'DESC')->take(28)->with('tweets', 'followers')->get();
            return $results;
        }

}
