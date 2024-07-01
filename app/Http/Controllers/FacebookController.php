<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Facebook\Facebook;

class FacebookController extends Controller
{
    protected $fb;

    public function __construct()
    {
        
        $this->fb = new Facebook([
            'app_id' => 1421656072563257,
            'app_secret' => "8d0a3a14e4d5ba6596800878a3b1c35d",
            'default_graph_version' => 'v12.0',
        ]);
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->scopes(['publish_actions'])->redirect();
    }

    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->user();
        
        $user = User::firstOrCreate([
            'facebook_id' => $facebookUser->getId(),
        ], [
            'name' => $facebookUser->getName(),
            'email' => $facebookUser->getEmail(),
            'facebook_token' => $facebookUser->token,
        ]);

        Auth::login($user, true);

        return redirect()->intended('/');
    }

    public function postToFacebook(Request $request)
    {
        $user = Auth::user();
        $message = $request->input('message');
        $pageAccessToken = $user->facebook_token;

        try {
            $response = $this->fb->post(
                '/me/feed',
                ['message' => $message],
                $pageAccessToken
            );
            return response()->json(['success' => true, 'response' => $response->getDecodedBody()]);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            return response()->json(['error' => 'Graph returned an error: ' . $e->getMessage()], 500);
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return response()->json(['error' => 'Facebook SDK returned an error: ' . $e->getMessage()], 500);
        }
    }
}
