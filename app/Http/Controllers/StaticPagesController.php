<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Auth;

class StaticPagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        $feed_items = [];
        if (Auth::check()) {
            $feed_items = Auth::user()->feed()->paginate(30);
        }
        return view('home', compact('feed_items'));
    }

   public function help()
   {
        return view('help');
   }

   public function about()
   {
        return view('about');
   }
}
