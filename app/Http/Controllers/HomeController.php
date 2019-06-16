<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $followed   = Auth::user()->following()->pluck('leader_id');
        $followed[] = Auth::user()->id;

        $unfollowed  = User::whereNotIn('id', $followed)->pluck('id');
        $suggestedIds = \DB::table('followers')->whereIn('follower_id', $followed)->whereIn('leader_id', $unfollowed)->pluck('leader_id');
        $suggested = count(User::whereIn('id', $suggestedIds)->take(6)->get()) ? User::whereIn('id', $suggestedIds)->take(6)->get(): User::whereNotIn('id', $followed)->take(6)->get();
        return view('home', compact('suggested'));
    }
}
