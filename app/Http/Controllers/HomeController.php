<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Adldap\Laravel\Facades\Adldap;

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
//        $users = Adldap::search()->users()->get();
//        var_dump($users);


//        $search = Adldap::search()->where('cn', '=', 'Karen Kuba')->get();
        
//        $user = Adldap::search()->find('gmitev');
//        var_dump($user);

        echo "<br>HomeController.php index";

        $user = auth()->user();

        if($user->hasAnyRole(['client_admin','type_client'])){
            return redirect('overview');
        }
        
        return view('home',['users'=>null]);
    }

    /**
     * Accept or Decline terms
     * @param $isAccept
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function acceptTerm($isAccept)
    {
        if ($isAccept) {
            $authUser = auth()->user();
            $authUser->signed_terms = 1;
            $authUser->save();

            return redirect('welcome');
        }

        auth()->logout();
        return redirect('/');
    }
    
    public function clearcache(){
        Cache::flush();
        return redirect('home')->with('success', 'Profile updated!');
    }
    
    public function privacy(){
        return view('privacy');
    }
    
    public function terms(){
        return view('terms');
    }    
    
    public function about(){
        return view('about');
    }
}
