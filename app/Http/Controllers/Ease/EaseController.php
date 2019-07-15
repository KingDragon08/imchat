<?php

namespace App\Http\Controllers\Ease;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EaseService;

use Validator;
use Exception;

class EaseController extends Controller {
    /**
     * 从环信获取好友列表
     * @param  Request $request [description]
     * @return json
     */
    public function friends(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }
        try {
            $data = EaseService::friends($request->username);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '获取好友列表失败']);
        }
    }

    /**
     * 请求添加好友
     * @param  Request $request [description]
     * @return json
     */
    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|int|exists:user,id',
            // 'message' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            EaseService::add($request->id, $request->userId, $request->input('message', ''));
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '请求添加好友失败']);
        }
    }

    /**
     * 同意添加好友
     * @param  Request $request [description]
     * @return json
     */
    public function agree(Request $request) {
        $validator = Validator::make($request->all(), [
            'addId' => 'required|int|exists:addfriends,id',
            'from' => 'required|int'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            EaseService::agree($request->addId, $request->from, $request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '同意添加好友失败']);
        }
    }

    /**
     * 拒绝添加好友
     * @param  Request $request [description]
     * @return json
     */
    public function decline(Request $request) {
        $validator = Validator::make($request->all(), [
            'addId' => 'required|int|exists:addfriends,id',
            'from' => 'required|int'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            EaseService::decline($request->addId, $request->from, $request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '同意添加好友失败']);
        }

    }

    /**
     * 添加好友历史
     * @param  Request $request [description]
     * @return json
     */
    public function history(Request $request) {
        $validator = Validator::make($request->all(), [
            'page' => 'int|min:1',
            'size' => 'int|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $page = max($request->input('page', 1), 1);
            $size = max($request->input('size', 10), 10);
            $data = EaseService::history($request->id, $page, $size);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '获取数据失败']);
        }

    }

    /**
     * 获取黑名单
     * @param  Request $request [description]
     * @return json
     */
    public function getBlackList(Request $request) {
        try {
            $data = EaseService::getBlackList($request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '获取数据失败']);
        }
    }

    /**
     * 添加黑名单
     * @param  Request $request [description]
     * @return json
     */
    public function addBlackList(Request $request) {
        $validator = Validator::make($request->all(), [
            'tid' => 'required|int|exists:user,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            EaseService::addBlackList($request->id, $request->tid);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        }
    }

    /**
     * 删除黑名单
     * @param  Request $request [description]
     * @return json
     */
    public function deleteBlackList(Request $request) {
        $validator = Validator::make($request->all(), [
            'tid' => 'required|int|exists:user,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            EaseService::deleteBlackList($request->id, $request->tid);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        }
    }

    /**
     * 创建群组
     * @param  Request $request [description]
     * @return json
     */
    public function createGroup(Request $request) {
        $validator = Validator::make($request->all(), [
            'groupname' => 'required|string|min:1',
            'desc' => 'required|string|min:1',
            'owner' => 'required|string|min:1',
            'members' => 'required|string|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $groupId = EaseService::createGroup($request->id, $request->groupname, $request->desc, $request->owner, $request->members);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $groupId]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        }
    }

    /**
     * 获取群组信息
     * @param  Request $request [description]
     * @return json
     */
    public function groupInfo(Request $request) {
        $validator = Validator::make($request->all(), [
            'groupId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = EaseService::groupInfo($request->groupId);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        }
    }


    /**
     * 更改群组信息：名称、描述
     * @param  Request $request [description]
     * @return json
     */
    public function changeGroupInfo(Request $request) {
        $validator = Validator::make($request->all(), [
            'groupId' => 'required',
            'newName' => 'required|string',
            'newDesc' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            EaseService::changeGroupInfo($request->groupId, $request->newName, $request->newDesc);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        }   

    }

    /**
     * 更改群组信息：头像
     * @param  Request $request [description]
     * @return json
     */
    public function changeGroupAvatar(Request $request) {
        $validator = Validator::make($request->all(), [
            'groupId' => 'required',
            'url' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            EaseService::changeGroupAvatar($request->groupId, $request->url);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        }
    }

    public function addMembers(Request $request) {
        $validator = Validator::make($request->all(), [
            'groupId' => 'required',
            'members' => 'required|string|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            EaseService::addMembers($request->groupId, $request->members);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        }   
    }

    /**
     * 获取自定类型的聊天室
     * @param  Request $request [description]
     * @return json
     */
    public function chatRoom(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = EaseService::chatRoom($request->type);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        }
    }

    /**
     * 获取聊天室信息
     * @param  Request $request [description]
     * @return json
     */
    public function roomInfo(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = EaseService::roomInfo($request->roomId);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        }
    }

    /**
     * 加入聊天室
     * @param  Request $request [description]
     * @return json
     */
    public function joinChatRoom(Request $request) {
        $validator = Validator::make($request->all(), [
            'roomId' => 'required',
            'username' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = EaseService::joinChatRoom($request->roomId, $request->username);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        }
    }



}
