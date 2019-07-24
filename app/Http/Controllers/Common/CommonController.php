<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CommonService;
use App\Services\UserService;

use Validator;
use Exception;

class CommonController extends Controller {
    /**
     * 获取广告
     * @param  Request $request [description]
     * @return json
     */
    public function ads(Request $request) {
        try{
            $data = CommonService::getAds();
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }
    }

    /**
     * 获取游戏
     * @param  Request $request [description]
     * @return json
     */
    public function games(Request $request) {
        try{
            $data = CommonService::getGames();
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }
    }

    /**
     * 获取配置
     * @param  Request $request [description]
     * @return json
     */
    public function config(Request $request) {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try{
            $data = CommonService::getConfig($request->key);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }

    }

    /**
     * 获取游戏房间列表
     * @param  Request $request [description]
     * @return json
     */
    public function rooms(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try{
            $data = CommonService::getGameRooms($request->type);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }
    }

    public function avatar(Request $request, $username) {
        $validator = Validator::make(['username' => $username], [
            'username' => 'required|string|exists:user,username'
        ]);
        $avatar = '';
        if ($validator->fails()) {
            $avatar = 'http://via.placeholder.com/200/000000/ff0000?text=FUCK';
        } else {
            $avatar = UserService::getAvatar($username)['avatar'];
        }
        $opts= [
            "http" => [
                "method"=>"GET",
                "timeout"=>3
            ],
        ];
        $context = stream_context_create($opts);
        return response(file_get_contents($avatar, false, $context), 200, [
            'Content-Type' => 'image',
        ]);
    }


}
