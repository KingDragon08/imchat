<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\NiuniuService;

use Validator;
use Exception;

class NiuniuController extends Controller {
    /**
     * 创建游戏
     * @param  Request $request [description]
     * @return json
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required',
            'banker' => 'required|exists:user,username',
            'jifen' => 'required|int'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }
        try{
            $data = NiuniuService::create($request->roomId, $request->banker, $request->jifen);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }
    }

    /**
     * 下注
     * @param  Request $request [description]
     * @return json
     */
    public function bet(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required',
            'bet' => 'required|int|min:0',
            'type' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }
        if (!in_array($request->type, ['normal', 'showHand'])) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);   
        }
        try{
            $msg = NiuniuService::bet($request->id, $request->roomId, $request->bet, $request->type);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $msg]);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }
    }

    /**
     * 停止下注
     * @param  Request $request [description]
     * @return json
     */
    public function end(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }
        try{
            $str = NiuniuService::end($request->roomId);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $str]);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }
    }

    /**
     * 发送红包
     * @param  Request $request [description]
     * @return json
     */
    public function sendBonus(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }
        try{
            $data = NiuniuService::sendBonus($request->roomId);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }
    }

    /**
     * 配置规则
     * @param  Request $request [description]
     * @return json
     */
    public function setConfig(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required',
            'cfg' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }
        try{
            NiuniuService::setConfig($request->roomId, $request->cfg);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }
    }

    /**
     * 获取规则
     * @param  Request $request [description]
     * @return json
     */
    public function getConfig(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }
        try{
            $data = NiuniuService::getConfig($request->roomId);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }
    }

    /**
     * 开牛牛红包
     * @param  Request $request [description]
     * @return json
     */
    public function openBonus(Request $request) {
        $validator = Validator::make($request->all(), [
            'bonusId' => 'required|int|exists:bonus,id',
            'roomId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = NiuniuService::openBonus($request->roomId, $request->bonusId, $request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 结算并发送账单
     * @param  Request $request [description]
     * @return json
     */
    public function result(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = NiuniuService::result($request->roomId);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            dd($e);
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 富豪榜
     * @param  Request $request [description]
     * @return json
     */
    public function bang(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = NiuniuService::bang($request->roomId);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 牛群列表
     * @param  Request $request [description]
     * @return json
     */
    public function list(Request $request) {
        try {
            $data = NiuniuService::list();
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }   
    }

    /**
     * 重推
     * @param  Request $request [description]
     * @return json
     */
    public function reset(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = NiuniuService::reset($request->roomId);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }





}
