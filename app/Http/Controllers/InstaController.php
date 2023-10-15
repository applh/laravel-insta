<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\InstaUser;
use App\Models\InstaMedia;
use App\Models\User;

class InstaController extends Controller
{
    static $insta_refresh_access_token = "https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=";
    static $insta_me = "https://graph.instagram.com/me?fields=id,username&access_token=";
    static $insta_graph = "https://graph.instagram.com/";
    static $insta_graph_media = "/media?fields=id,username,timestamp,caption,permalink,media_type,media_url&access_token=";
    static $pagination = 2;

    public function home()
    {
        // get a list of 10 Users ordre by created_at DESC 
        $recent_users = User::orderByDesc('created_at')->take(10)->get();

        // get the list of InstaMedia
        $pagination = env('INSTA_PAGINATION', static::$pagination);
        // find all InstaMedia
        $insta_media = InstaMedia::orderByDesc('insta_media_timestamp')
            ->paginate($pagination);

        return view('insta_home', [
            'recent_users' => $recent_users,
            'insta_media' => $insta_media,
        ]);
    }

    public function user($name)
    {
        // find User with name as $page_user_name
        $page_user = User::where('name', $name)->first();
        if (!$page_user) {
            return redirect('/');
        }
        else {
            // get InstaUser
            $insta_user = InstaUser::where('user_id', $page_user->id)->first();
            if ($insta_user) {
                $insta_user_id = $insta_user->insta_id;
            }
        }
        if ($insta_user_id ?? false) {
            // find InstaUser with insta_id
            $pagination = env('INSTA_PAGINATION', static::$pagination);
            // find all InstaMedia with user_id
            $insta_media = InstaMedia::where('insta_user_id', $insta_user_id)
                ->orderByDesc('insta_media_timestamp')
                ->paginate($pagination);
        }

        return view('insta_user', [
            'page_user' => $page_user,
            'insta_media' => $insta_media ?? null,
        ]);
    }

    public function dashboard(Request $request)
    {
        // pagination
        $pagination = env('INSTA_PAGINATION', static::$pagination);

        // get user
        $user = $request->user();
        $insta_access_token = "";
        $user_id = $user->id;

        // find InstaUser with user_id
        $insta_user = \App\Models\InstaUser::where('user_id', $user_id)->first();
        if ($insta_user) {
            $insta_user_id0 = $insta_user->id;
            $insta_user_id = $insta_user->insta_id;
            $insta_access_token = $insta_user->access_token;
            $insta_username = $insta_user->insta_username;
        }

        if ($insta_user_id ?? false) {
            // find all InstaMedia with user_id
            $insta_media = \App\Models\InstaMedia::where('insta_user_id', $insta_user_id)
                ->orderBy('insta_media_timestamp', 'desc')
                ->paginate(2);
            $nb_insta_media = count($insta_media);

            // cron url
            // get the full URL to the cron route
            $insta_cron_url = url('/insta_cron/' . $insta_user_id0 . '/' . md5($insta_user_id));
        }
        return view('dashboard', [
            'insta_access_token' => $insta_access_token,
            'insta_username' => $insta_username ?? "",
            'insta_cron_url' => $insta_cron_url ?? "",
            'insta_media' => $insta_media ?? [],
            'nb_insta_media' => $nb_insta_media ?? 0,
            'insta_user' => $insta_user ?? null,
            'insta_user_id' => $insta_user_id ?? '',
            'insta_user_id0' => $insta_user_id0 ?? '',
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

    function cron(Request $request, $id, $md5)
    {
        // $id from model InstaUser
        // $md5 from model InstaUser md5(insta_id)

        $insta_api_data = [];
        $insta_api_data["date"] = date("Y-m-d H:i:s");
        $insta_api_data["id"] = $id;
        $insta_api_data["md5"] = $md5;

        // get user by $id
        $insta_user = InstaUser::find($id);
        if ($insta_user) {
            // get insta_id
            $insta_id = $insta_user->insta_id ?? "";
            // check md5
            $insta_id_md5 = md5($insta_id);
            if ($insta_id_md5 == $md5) {
                // get access_token
                $access_token = $insta_user->access_token ?? "";
                // get user_id
                $user_id = $insta_user->user_id ?? "";
                // get insta_api_data
                $insta_api_data = static::web_insta_api($access_token, $user_id);
            } else {
                $insta_api_data["error"] = "md5 not match";
            }
        }

        // better form processing with ajax + json
        // https://laravel.com/docs/10.x/responses#json-responses
        return response()->json($insta_api_data);
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
