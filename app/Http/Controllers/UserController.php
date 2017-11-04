<?php

namespace App\Http\Controllers;

use App\Account;
use App\Scan;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public static function httpGet($url)
    {
        return file_get_contents($url);
    }

    public static function getAppAccessToken(){
        $request =  UserController::httpGet('https://graph.facebook.com/oauth/access_token?client_id='.env('FACEBOOK_APP_ID').'&client_secret='.env('FACEBOOK_APP_SECRET').'&grant_type=client_credentials');
        $json = json_decode($request, true);
        return $json['access_token'];
    }

    public static function getTokenInfo($access_token){
        $request = UserController::httpGet('https://graph.facebook.com/debug_token?input_token=' . $access_token .'&access_token='. UserController::getAppAccessToken());
        $json = json_decode($request, true);
        return $json;
    }

    public static function isAccessTokenValid($access_token){
        $json = UserController::getTokenInfo($access_token);
        return $json['data']['is_valid'];
    }

    public static function updateDataFromFacebook($access_token){
        $request = UserController::httpGet('https://graph.facebook.com/me?fields=id,name,email,gender&access_token=' . $access_token);
        $json = json_decode($request, true);
        $id = $json['id'];
        $name = array_key_exists('name', $json) ? $json['name'] : null;
        $email = array_key_exists('email', $json) ? $json['email'] : null;
        $gender = array_key_exists('gender', $json) ? $json['gender'] : null;
        $account = Account::find($id);
        if (!$account) {
            $account = Account::create(['name' => $name, 'id' => $id, 'email' => $email, 'gender' => $gender, 'points' => 0, 'scanned' => []]);
        } else {
            $account->update(['name' => $name, 'email' => $email, 'gender' => $gender]);
        }
        return $account;
    }

    public function extendToken(Request $request){
        $access_token = $request->get('access_token');
        $request = UserController::httpGet('https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id='.env('FACEBOOK_APP_ID').'&client_secret='.env('FACEBOOK_APP_SECRET').'&fb_exchange_token=' . $access_token);
        $json = json_decode($request, true);
        return $json['access_token'];
    }

    /*
    public function checkToken(Request $request){
        $access_token = $request->get('access_token');
        $json = UserController::getTokenInfo($access_token);
        if ($json['data']['is_valid']){
            echo "Is Valid";
        }
        else{
            echo $json['data']['error']['message'];
        }
    }
    */

    public function getMe(Request $request){
        $access_token = $request->get('access_token');

        if(!UserController::isAccessTokenValid($access_token)){
            echo "100";
            return;
        }

        $account = UserController::updateDataFromFacebook($access_token);
        echo $account->toJson();
    }

    public function submitScan(Request $request){
        $access_token = $request->get('access_token');

        if(!UserController::isAccessTokenValid($access_token)){
            echo "100";
            return;
        }

        $scannedId = $request->get('scannedId');
        $account = UserController::updateDataFromFacebook($access_token);

        if (!in_array($scannedId, $account->scanned)){
            $points = UserController::getPoints($scannedId);
            if($points < 0){
                echo "-1"; //Invalid id
            }
            else{
                $account->points += $points;
                array_push($account->scanned, $scannedId);
                $account->save();
                echo "1"; //Succeeded
            }
        }
        else{
            echo "0"; //Already scanned
        }
    }

    public static function getPoints($scannedId){
        $realId = base64_decode($scannedId);
        $scan = Scan::find($realId);
        if(!$scan){
            return -1;
        }
        return $scan->points;
    }
}