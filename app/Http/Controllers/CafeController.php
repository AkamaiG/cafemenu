<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;

class CafeController extends Controller
{
	private function error($errorCode, $errorDesc)
	{
		return response()->json([
                'success' => 'false',
                'error' => $errorCode,
                'error_message' => $errorDesc
            ]);
	}

	private function getUserid ($token)
    {
        $userrow = DB::table('auth')->where('api_token', $token)->first();

        return $userrow->vkid;
    }
	
    public function getCafeList(Request $request)
    {
		$headers = [ 'Content-Type' => 'application/json; charset=utf-8' ];
		
        if ($request->input('api_token') == null)
        {
            return 1;
        }
        $api_token = $request->input('api_token');

        $checktoken = DB::table('auth')->where('api_token', $api_token)->first();

        if ($checktoken == null)
        {
			return $this->error(401, 'Unauthorized');
        }else{
            $cafelist = DB::table('cafelist')->get()->toArray();

            return response()->json([
                'success' => 'true',
                'result' => $cafelist
            ],200, $headers, JSON_UNESCAPED_UNICODE);
        }
    }

    public function getUserInfoVk(Request $request)
    {
        if ($request->input('token') == null)
        {
			return $this->error(400, 'Bad Request');
        }
        else{
            $token = $request->input('token');
        }

        $url = 'https://api.vk.com/method/users.get';
        $params = array(
			'fields' => 'photo_max',
            'access_token' => $token,
            'v' => '5.64',
        );

        $response = file_get_contents($url, false, stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params)
            )
        )));

        $response = json_decode($response);
        $response = $response->response;
        for ($i = 0; $i < count($response); $i++) {
            $id = $response[$i]->id;
            $first = $response[$i]->first_name;
            $last = $response[$i]->last_name;
			$photo = $response[$i]->photo_max;
        };

        $checkdata = DB::table('auth')->where('vkid', $id)->first();
		
		$headers = [ 'Content-Type' => 'application/json; charset=utf-8' ];

        if ($checkdata == null)
        {
            $gentoken = str_random(60);

            DB::table('auth')->insert(
                ['vkid' => $id, 'first_name' => $first, 'last_name' => $last, 'photo' => $photo, 'api_token' => $gentoken]
            );

            return response()->json([
                'success' => 'true',
                'result' => array(
                    'id' => $id,
                    'first_name' => $first,
                    'last_name' => $last,
					'photo' => $photo
                )
            ],200, $headers, JSON_UNESCAPED_UNICODE)->cookie('api_token', $gentoken, 0);
        }else{
            return response()->json([
                'success' => 'true',
                'result' => array(
                    'id' => $id,
                    'first_name' => $first,
                    'last_name' => $last,
					'photo' => $photo
                )
            ],200, $headers, JSON_UNESCAPED_UNICODE)->cookie('api_token', $checkdata->api_token, 0);
        }
    }

    public function getcafedata(Request $request)
    {
        if ($request->input('api_token') == NULL)
        {
            return $this->error(400, 'Bad Request (Token)');
        }

        if ($request->input('lat') == null || $request->input('long') == null)
        {
            return $this->error(400, 'Bad Request (lat and long)');
        }else{
            $results = DB::table('cafelist')->get()->toArray();

            $max = 50;

            $lat = $request->input('lat');
            $long = $request->input('long');
			
			$headers = [ 'Content-Type' => 'application/json; charset=utf-8' ];

            foreach($results as $row)
            {
                $latdb = $row->lat;
                $longdb = $row->long;

                $lat1=deg2rad($lat);
                $lng1=deg2rad($long);
                $lat2=deg2rad($latdb);
                $lng2=deg2rad($longdb);

                $cords = round( 6378137 * acos( cos( $lat1 ) * cos( $lat2 ) * cos( $lng1 - $lng2 ) + sin( $lat1 ) * sin( $lat2 ) ) );

                if ($cords < $max)
                {
                    $cafecategories = DB::table('categories')->where('cafe_id', $row->id)->get()->toArray();
                    $cafedishes = DB::table('dishes')->where('cafe_id', $row->id)->get()->toArray();
                    return response()->json([
                        'success' => true,
                        'result' => array(
                            'cafe' => array(
                                'id' => $row->id,
                                'name' => $row->name,
                                'desc' => $row->desc,
                                'image' => $row->image,
								'rating' => $row->rating,
								'work' => $row->work
                            ),
                            'categories' => $cafecategories,
                            'dishes' => $cafedishes
                            )
                    ],200, $headers, JSON_UNESCAPED_UNICODE);
                }else{
                    return $this->error(400, 'Bad Request (No data response)');
                }
            }
        }
    }

    public function getcafemenu(Request $request)
    {
        $cafe_id = $request->input('cafe_id');
        $token = $request->input('api_token');

        if ($cafe_id == NULL || $token == NULL)
        {
            return $this->error(400, 'Bad Request (No cafe id and token)');
        }

        $checktoken = DB::table('auth')->where('api_token', $token)->first();

        if ($checktoken)
        {
            $cafelist = DB::table('cafelist')->where('id', $cafe_id)->get()->toArray();
            $cafecategories = DB::table('categories')->where('cafe_id', $cafe_id)->get()->toArray();
            $cafedishes = DB::table('dishes')->where('cafe_id', $cafe_id)->get()->toArray();
			
			$headers = [ 'Content-Type' => 'application/json; charset=utf-8' ];
			
			foreach($cafelist as $cfl)
			{
				return response()->json([
                'success' => true,
                'result' => array(
                    'cafe' => array(
                                'id' => $cfl->id,
                                'name' => $cfl->name,
                                'desc' => $cfl->desc,
                                'image' => $cfl->image,
								'rating' => $cfl->rating,
								'work' => $cfl->work
                            ),
                    'categories' => $cafecategories,
                    'dishes' => $cafedishes
                )
            ],200, $headers, JSON_UNESCAPED_UNICODE);
			}
        }else{
			return $this->error(401, 'Unauthorized');
        }
    }

    public function addReviews(Request $request)
    {
        $token = $request->input('api_token');
        $cafe_id = $request->input('cafe_id');
        $desc = $request->input('desc');
        $review = $request->input('review');

        $rowauth = DB::table('auth')->where('api_token', $token)->first();

        if ($rowauth == NULL)
        {
			return $this->error(401, 'Unauthorized');
        }

        $checkreviewcount = DB::table('reviews')->where([
            ['vkid', '=', $rowauth->vkid],
            ['cafe_id', '=', $cafe_id],
        ])->count();

        if ($checkreviewcount == 0)
        {
            DB::table('reviews')->insert(
                ['cafe_id' => $cafe_id, 'vkid' => $rowauth->vkid, 'desc' => $desc, 'review' => $review, 'created_at' => date("d.m.Y H:i:s")]
            );

            return response()->json([
                'success' => 'true'
            ]);
        }else{
            return $this->error(400, 'Bad Request (1 review only)');
        }
    }

    public function delReviews (Request $request)
    {
        if ($request->input('api_token'))
        {
            return $this->error(401, 'Unauthorized');
        }

        $token = $request->input('api_token');
        $cafeid = $request->input('cafe_id');

        $rowauth = DB::table('auth')->where('api_token', $token)->first();

        $checkreview = DB::table('reviews')->where([
            ['vkid', '=', $rowauth->vkid],
            ['cafe_id', '=', $cafeid],
        ])->count();

        if ($checkreview >= 1)
        {
            DB::table('reviews')->where([
                ['vkid', '=', $rowauth->vkid],
                ['cafe_id', '=', $cafeid],
            ])->delete();

            return response()->json([
                'success' => 'true'
            ]);
        }else{
            return $this->error(400, 'Bad Request (No review)');
        }
    }

    public function getReviews (Request $request)
    {
        $cafe_id = $request->input('cafe_id');
        $token = $request->input('api_token');
        $count = $request->input('count');

        if ($token == NULL)
        {
			return $this->error(401, 'Unauthorized');
        }

        if ($cafe_id == NULL)
        {
            return $this->error(400, 'Bad Request (No cafe id)');
        }

        $rowauth = DB::table('auth')->where('api_token', $token)->first();

        $rowreviews = DB::table('reviews')->where('cafe_id', $cafe_id)->get()->toArray($count);

        $checkreview = DB::table('reviews')->where([
            ['vkid', '=', $rowauth->vkid],
            ['cafe_id', '=', $cafe_id],
        ])->get();
		
		$headers = [ 'Content-Type' => 'application/json; charset=utf-8' ];

        if (!$checkreview)
        {
			return response()->json([
                'success' => true,
                'result' => $rowreviews,
				$checkreview
            ],200, $headers, JSON_UNESCAPED_UNICODE);
        }else{
            return response()->json([
                'success' => true,
                'result' => $rowreviews
            ],200, $headers, JSON_UNESCAPED_UNICODE);
        }
    }
	
	public function userReviews (Request $request)
	{
		if ($request->input('api_token') == NULL)
		{
			return $this->error(401, 'Unauthorized');
		}else{
			$usertoken = $request->input('api_token');
			
			$userid = $this->getUserid($usertoken);
		}
		
		$reviewlist = DB::table('reviews')->where('vkid', $userid)->get()->toArray();
		
		$headers = [ 'Content-Type' => 'application/json; charset=utf-8' ];
		
		return response()->json([
                'success' => true,
                'result' => $reviewlist
            ],200, $headers, JSON_UNESCAPED_UNICODE);
	}
	
	public function updRating ()
	{
		$cafelist = DB::table('cafelist')->get()->toArray();
		foreach($cafelist as $cl)
		{
			$rowreviews = DB::table('reviews')->where('cafe_id', $cl->id)->pluck('review');
		
			$nums = $rowreviews;
			$sum = 0;
			foreach($nums as $num){
				$sum += $num;
				$rating = $sum/count($nums);
				DB::table('cafelist')->where('id', $cl->id)->update(['rating' => $rating]);
			}
		}
	}
}