<?php

namespace App\Http\Controllers;

use App\Models\User;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;


class OAuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $value = $request->session()->get('authenticatedUser');       
    //     if ($value != null)
    //     {
    //         return redirect('/dashboard')->with('alreadyLoggedIn', 'Hey, you\'re already logged in.');
    //     } 
    //     return view('login');
    // }
    public function redirectToGoogle(Request $request)
    {
        return Socialite::driver('google')->stateless()->with(["prompt" => "select_account"])->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        // try {
            $user = Socialite::driver('google')->stateless()->user();
                 
            $finduser = User::where('email', $user->email)->first();
         
            if($finduser){
         
                Auth::login($finduser);
        
                return redirect()->intended('dashboard');
         
            }else{
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'password' => encrypt('katziondummyPasW0rD'),
                ]);
                Auth::login($newUser, true);
                // $newUser = DB::table('users')->updateOrInsert(['email' => $user->email],[
                //         'name' => $user->name,
                //         'password' => encrypt('katziondummyPasW0rD')
                //     ]);
         
                // Auth::login($newUser);
        
                return redirect('dashboard');
            }
        
        // } catch (Exception $e) {
        
        //     Log::error('Exception occurred: ' . $e->getMessage());
        //     return redirect('/welcome')->withErrors(['error' => 'An error occurred. Please try again later.']);
        // }
     
        
    }

    public function logout(Request $request)
    {
        $request->session()->put('authenticatedUser', null);
        return view('login');
    }
}
