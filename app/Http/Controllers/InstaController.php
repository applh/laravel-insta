<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\InstaUser;
use App\Models\InstaMedia;

class InstaController extends Controller
{
    static $insta_refresh_access_token = "https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=";
    static $insta_me = "https://graph.instagram.com/me?fields=id,username&access_token=";
    static $insta_graph = "https://graph.instagram.com/";
    static $insta_graph_media = "/media?fields=id,username,timestamp,caption,permalink,media_type,media_url&access_token=";

    public function home()
    {
        return view('insta_home');
    }

    public function user($name)
    {
        return view('insta_user', ['name' => $name]);
    }

    public function dashboard(Request $request)
    {
        // get logged in user
        $user = $request->user();

        // keep breeze dashboard
        return view('dashboard', [
            'user' => $user,
            'insta_access_token' => $insta_access_token ?? '',
        ]);
    }

    public function api(Request $request)
    {
        // get logged in user
        $user = $request->user();

        if ($user ?? false) {
            // validate access_token
            $request->validate([
                'insta_access_token' => ['required', 'string', 'max:255'],
            ]);

            // insta: user access token
            $insta_access_token = $request->input("insta_access_token", "");
            // trim done by laravel
            // https://laravel.com/docs/10.x/requests#input-trimming-and-normalization

            // get user_id
            $user_id = $user->id;
            static::web_insta_api($insta_access_token, $user_id);
        }

        // keep breeze dashboard
        return redirect('/dashboard');
    }

    static function web_insta_api($access_token, $user_id)
    {
        if (!$access_token) {
            return false;
        }

        try {
            $insta_me  = static::$insta_me . $access_token;
            // launch request
            $insta_me_response = static::api_json($insta_me);

            // get id from response
            $id = $insta_me_response["id"] ?? "";
            $username = $insta_me_response["username"] ?? "";
            if ($id && $username) {
                // save to database: $id, $username, $access_token
                // add to model InstaUser if $access_token not exists
                InstaUser::firstOrCreate(
                    [
                        'insta_id' => $id,
                    ],
                    [
                        'user_id' => $user_id,
                        'insta_username' => $username,
                        'access_token' => $access_token,
                        "access_token_expires_in" => "",
                    ]
                );

                // $insta_graph = "https://graph.instagram.com/$id?fields=id,ig_id,username,media&access_token=$access_token";
                $insta_graph_media = static::$insta_graph . $id . static::$insta_graph_media . $access_token;
                $insta_graph_response = static::api_json($insta_graph_media);

                // get data from response
                $data = $insta_graph_response["data"] ?? [];
                // loop on data and save to database
                foreach ($data as $key => $value) {
                    $insta_media_id = $value["id"] ?? "";
                    $insta_media_username = $value["username"] ?? "";
                    $insta_media_type = $value["media_type"] ?? "";
                    $insta_media_url = $value["media_url"] ?? "";
                    $insta_media_caption = $value["caption"] ?? "";
                    $insta_media_permalink = $value["permalink"] ?? "";
                    $insta_media_timestamp = $value["timestamp"] ?? "";
                    // save to database
                    // add to model InstaMedia if $insta_media_id not exists
                    InstaMedia::firstOrCreate(
                        [
                            'insta_media_id' => $insta_media_id,
                        ],
                        [
                            'insta_user_id' => $id,
                            'insta_media_username' => $insta_media_username,
                            'insta_media_type' => $insta_media_type,
                            'insta_media_url' => $insta_media_url,
                            'insta_media_caption' => $insta_media_caption,
                            'insta_media_permalink' => $insta_media_permalink,
                            'insta_media_timestamp' => $insta_media_timestamp,
                        ]
                    );
                }
            }
        } catch (RequestException $e) {
            $error = $e->getMessage();
        }

        $now = date("Y-m-d H:i:s");
        return [
            "date" => $now,
            "user_id" => $user_id ?? "",
            "error" => $error ?? "",
            "insta_me" => $insta_me_response ?? "",
            "id" => $id ?? "",
            "insta_graph" => $insta_graph_response ?? "",
        ];
    }

    static function api_json($url, $method = "GET")
    {
        // launch request
        $client = new Client();
        $response = $client->request($method, $url);
        $body = $response->getBody();
        $content = $body->getContents();
        $data = json_decode($content, true);
        return $data;
    }
}
