<?php

namespace App\Http\Controllers;

use App\Account;
use App\Scan;
use \Illuminate\Http\Request;

class UserController extends Controller
{
    public static function httpGet($url)
    {
        $ch = curl_init($url); // such as http://example.com/example.xml
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public static function getAppAccessToken(){
        $request =  UserController::httpGet('https:/graph.facebook.com/oauth/access_token?client_id='.env('FACEBOOK_APP_ID').'&client_secret='.env('FACEBOOK_APP_SECRET').'&grant_type=client_credentials');
        $json = json_decode($request, true);
        return $json['access_token'];
    }

    public static function getTokenInfo($access_token){
        $request = UserController::httpGet('https:/graph.facebook.com/debug_token?input_token=' . $access_token .'&access_token='. UserController::getAppAccessToken());
        $json = json_decode($request, true);
        return $json;
    }

    public static function isAccessTokenValid($access_token){
        $json = UserController::getTokenInfo($access_token);
        return $json['data']['is_valid'];
    }

    public static function updateDataFromFacebook($access_token){
        $request = UserController::httpGet('https://graph.facebook.com/me?fields=id,name,email&access_token=' . $access_token);
        $json = json_decode($request, true);
        $id = $json['id'];
        $name = is_null($json['name']) ? null : $json['name'];
        $email = is_null($json['email']) ? null : $json['email'];
        $account = Account::find($id);
        if (!$account) {
            $account = Account::create(['name' => $name, 'id' => $id, 'email' => $email, 'points' => 0, 'scanned' => []]);
        } else {
            $account->update(['name' => $name, 'email' => $email]);
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