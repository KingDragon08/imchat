<?php 
namespace App\Services;

use Storage;
use Exception;

class FilesService {

    const ALLOWED_TYPES = ['jpg', 'jpeg', 'gif', 'png', 'mov', 'MOV', 'mp4', 'mp3'];

    // 上传文件
    public static function upload($file) {
        if ($file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            if (!in_array($ext, self::ALLOWED_TYPES)) {
                throw new Exception("不允许的文件类型");
            }
            $realPath = $file->getRealPath();
            $type = $file->getClientMimeType();
            // 上传文件
            $filename = date('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $ext;
            $bool = Storage::disk('uploads')->put($filename, file_get_contents($realPath));
            if (!$bool) {
                throw new Exception("文件上传失败");    
            }
            return env('APP_URL') . 'uploads/' . $filename;
        } else {
            throw new Exception("文件上传失败");
        }
    }


}

?>