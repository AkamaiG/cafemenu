<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use DB;

class CafeController extends Controller
{
    public function getcafelist(Request $request)
    {
        $token = $request->api_token;
        if ($token == null)
        {
            return response()->json([
                'success' => 'false',
                'error' => 'true',
                'error_message' => 'not api_token'
            ]);
            exit();
        }else{
            $cafelist = DB::table('cafelist')->get();
            return $cafelist;
        }
    }

    public function getuserid(Request $request)
    {
        if ($request->token == null)
        {
            return response()->json([
                'success' => 'false',
                'error' => 'true',
                'error_message' => 'not token'
            ]);
            exit();
        }
        else{
            $token = $request->token;
        }

        $url = 'https://api.vk.com/method/users.get';
        $params = array(
            'access_token' => $token,
            'v' => 5.64,
        );

        $result = file_get_contents($url, false, stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params)
            )
        )));

        $result = json_decode($result);
        $result = $result->response;
        for ($i = 0; $i < count($result); $i++) {
            $this->checkRegister($result[$i]->uid);
        };
    }

    public function checkRegister($id)
    {
        $auth = DB::table('auth')->select('vkid', '$id')->get();
        if ($auth == null)
        {
            $this->getuserinfo($id, 0);
        }else{
            $this->getuserinfo($id, 1);
        }
    }

    public function getuserinfo($id, $check)
    {
        if ($check == 0) {
            $url = 'https://api.vk.com/method/users.get';
            $params = array(
                'user_ids' => $id,
            );

            $result = file_get_contents($url, false, stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query($params)
                )
            )));

            $raw = json_decode($result);
            $raw = $raw->response;
            for ($i = 0; $i < count($raw); $i++) {
                DB::table('auth')->insert(
                        ['vkid' => $raw[$i]['uid'], 'first_name' => $raw[$i]['first_name'], 'last_name' => $raw[$i]['last_name'], 'api_token' => str_random(60)]
                );
            }
        }else{
            $this->getinfodb($id);
        }
    }

    public function getinfodb($id)
    {
        $auth = DB::table('auth')->select('uid', $id)->get();
        return response()->json([
            'success' => 'true',
            'result' => array(
                'uid' => $id,
                'first_name' => $auth->first_name,
                'last_name' => $auth->last_name,
            )
        ]);
    }
}
