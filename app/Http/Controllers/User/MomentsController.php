<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MomentsService;

use Validator;
use Exception;

class MomentsController extends Controller {
    /**
     * 获取朋友圈
     * @param  Request $request [description]
     * @return json
     */
    public function get(Request $request) {
        try {
            $page = max($request->input('page', 1), 1);
            $size = max($request->input('size', 10), 10);
            $data = MomentsService::get($request->id, $page, $size);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '获取朋友圈数据失败']);
        }
    }

    /**
     * 同步朋友圈
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function syncMoments(Request $request) {
        try {
            $data = MomentsService::sync($request->input('currentLocalMaxMessageId', -1), $request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            dd($e);
            return response()->json(['status' => 1, 'msg' => '同步朋友圈数据失败']);   
        }
    }

    /**
     * 发布朋友圈
     * @param  Request $request [description]
     * @return json
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:1',
            // 'imgs' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
            // return response()->json(['status' => 1, 'msg' => $validator->errors()]);
        }

        try {
            MomentsService::create($request->id, $request->content, $request['imgs'] ?? '', $request['video'] ?? '');
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '发布朋友圈数据失败']);
        }
    }

    /**
     * 点赞
     * @param  Request $request [description]
     * @return json
     */
    public function like(Request $request) {
        $validator = Validator::make($request->all(), [
            'momentsId' => 'required|int|exists:moments,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            MomentsService::like($request->id, $request->momentsId);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '点赞失败']);
        }
    }

    /**
     * 评论
     * @param  Request $request [description]
     * @return json
     */
    public function comment(Request $request) {
        $validator = Validator::make($request->all(), [
            'momentsId' => 'required|int|exists:moments,id',
            'content' => 'required|string|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }
        try {
            MomentsService::comment($request->id, $request->momentsId, $request->content);
            return response()->json(['status' => 0, 'msg' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '评论失败']);
        }
    }

    public function getMoments(Request $request) {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|int|exists:user,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $page = max($request->input('page', 1), 1);
            $size = max($request->input('size', 10), 10);
            $data = MomentsService::getMoments($request->userId, $page, $size);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '获取朋友圈失败']);
        }


    }



}
