<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CommonService;

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


}
