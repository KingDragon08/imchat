<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FilesService;

use Validator;
use Exception;

class FilesController extends Controller {
    /**
     * 获取朋友圈
     * @param  Request $request [description]
     * @return json
     */
    public function upload(Request $request) {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => implode(',', $validator->errors()->all())]);
        }
        try{
            $url = FilesService::upload($request->file('file'));
            return response()->json(['status' => 0, 'msg' => 'ok', 'url' => $url]);
        } catch (Exception $e) {
            return response()->json(array('status' => 1, 'msg' => $e->getMessage()));
        }

    }

}
