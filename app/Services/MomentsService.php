<?php 
namespace App\Services;
use App\Models\UserModel;
use App\Models\MomentsModel;
use App\Models\CommentsModel;
use App\Models\ThumbModel;

use App\Services\EaseService;

use Cache;
use Exception;
use DB;


class MomentsService {
    /**
     * 获取
     * @param  int    $id   用户id
     * @param  int    $page 页码
     * @param  int    $size 单页数据量
     * @return array
     */
    public static function get(int $id, int $page, int $size) {
        $user = UserModel::select(['id', 'username', 'nickname', 'avatar'])->where('id', $id)->first();
        $friends = EaseService::friends($user->username)['chat'];
        $friends[] = [
            'id' => $user->id,
            'nickname' => $user->nickname,
            'username' => $user->username,
            'avatar' => $user->avatar
        ];
        $data = MomentsModel::select('*')->whereIn('userId', array_column($friends, 'id'))->orderBy('id', 'desc')
                ->offset(($page - 1) * $size)->limit($size)->get()->toArray();
        foreach ($data as &$item) {
            $index = array_search($item['userId'], array_column($friends, 'id'));
            $item['username'] = $friends[$index]['username'];
            $item['nickname'] = $friends[$index]['nickname'];
            $item['avatar'] = $friends[$index]['avatar'];
            $arr = ['comments.id', 'comments.content', 'comments.userId', 'comments.timestamp', 'user.nickname', 'user.username', 'user.avatar'];
            $item['comments'] = DB::table('comments')->select($arr)->where('momentsId', $item['id'])
                                ->join('user', function ($join) use ($friends) {
                                    $join->on('comments.userId', '=', 'user.id')
                                        ->where('comments.userId', 'in', array_column($friends, 'id'));
                                })
                                ->get()->toArray();
            $arr = ['thumb.id', 'thumb.userId', 'thumb.timestamp', 'user.nickname', 'user.username', 'user.avatar'];
            $item['thumb'] = DB::table('thumb')->select($arr)->where('momentsId', $item['id'])
                                ->join('user', function ($join) use ($friends) {
                                    $join->on('thumb.userId', '=', 'user.id')
                                        ->where('thumb.userId', 'in', array_column($friends, 'id'));
                                })
                                ->get()->toArray();
        }
        return $data;
    }

    /**
     * 同步服务端和app缓存朋友圈信息
     * @param  [type] $id app缓存的最新一条朋友圈id
     * @return [type]     [description]
     */
    public static function sync($id, $userId) {
        if ($id == -1) {
            return [];
        }
        $user = UserModel::select(['id', 'username', 'nickname', 'avatar'])->where('id', $userId)->first();
        $friends = EaseService::friends($user->username)['chat'];
        $friends[] = [
            'id' => $user->id,
            'nickname' => $user->nickname,
            'username' => $user->username,
            'avatar' => $user->avatar
        ];
        $data = MomentsModel::select('*')->whereIn('userId', array_column($friends, 'id'))
                ->where([['id', '>', $id]])
                ->orderBy('id', 'desc')
                ->get()->toArray();
        foreach ($data as &$item) {
            $index = array_search($item['userId'], array_column($friends, 'id'));
            $item['username'] = $friends[$index]['username'];
            $item['nickname'] = $friends[$index]['nickname'];
            $item['avatar'] = $friends[$index]['avatar'];
            $arr = ['comments.id', 'comments.content', 'comments.userId', 'comments.timestamp', 'user.nickname', 'user.username', 'user.avatar'];
            $item['comments'] = DB::table('comments')->select($arr)->where('momentsId', $item['id'])
                                ->join('user', function ($join) use ($friends) {
                                    $join->on('comments.userId', '=', 'user.id')
                                        ->where('comments.userId', 'in', array_column($friends, 'id'));
                                })
                                ->get()->toArray();
            $arr = ['thumb.id', 'thumb.userId', 'thumb.timestamp', 'user.nickname', 'user.username', 'user.avatar'];
            $item['thumb'] = DB::table('thumb')->select($arr)->where('momentsId', $item['id'])
                                ->join('user', function ($join) use ($friends) {
                                    $join->on('thumb.userId', '=', 'user.id')
                                        ->where('thumb.userId', 'in', array_column($friends, 'id'));
                                })
                                ->get()->toArray();
        }
        return $data;
    }

    /**
     * 获取单个人的朋友圈
     * @param  int    $userId   [description]
     * @param  int    $page [description]
     * @param  int    $size [description]
     * @return [type]       [description]
     */
    public static function getMoments(int $userId, int $page, int $size) {
        $data = MomentsModel::select('*')->where('userId', $userId)->orderBy('id', 'desc')
                ->offset(($page - 1) * $size)->limit($size)->get()->toArray();
        $user = UserModel::select(['id', 'username', 'nickname', 'avatar'])->where('id', $userId)->first();
        foreach ($data as &$item) {
            $item['username'] = $user->username;
            $item['nickname'] = $user->nickname;
            $item['avatar'] = $user->avatar;
            $arr = ['comments.id', 'comments.content', 'comments.userId', 'comments.timestamp', 'user.nickname', 'user.username', 'user.avatar'];
            $item['comments'] = DB::table('comments')->select($arr)->where('momentsId', $item['id'])
                                ->join('user', function ($join) {
                                    $join->on('comments.userId', '=', 'user.id');
                                })
                                ->get()->toArray();
            $arr = ['thumb.id', 'thumb.userId', 'thumb.timestamp', 'user.nickname', 'user.username', 'user.avatar'];
            $item['thumb'] = DB::table('thumb')->select($arr)->where('momentsId', $item['id'])
                                ->join('user', function ($join) {
                                    $join->on('thumb.userId', '=', 'user.id');
                                })
                                ->get()->toArray();
        }
        return $data;
    }

    /**
     * 创建朋友圈
     * @param  int    $id      [description]
     * @param  string $content [description]
     * @param  string $imgs    [description]
     * @param  string $video   [description]
     * @return [type]          [description]
     */
    public static function create(int $id, string $content, string $imgs='', string $video='') {
        $momentsModel = new MomentsModel();
        $momentsModel->userId = $id;
        $momentsModel->content = $content;
        $momentsModel->imgs = $imgs;
        $momentsModel->video = $video;
        $momentsModel->timestamp = time();
        $momentsModel->save();
    }


    /**
     * 点赞
     * @param  int    $id        [description]
     * @param  int    $momentsId [description]
     * @return [type]            [description]
     */
    public static function like(int $id, int $momentsId) {
        $count = ThumbModel::select('*')->where('momentsId', $momentsId)
                ->where('userId', $id)->count();
        if ($count > 0) {
            return;
        }
        $thumbModel = new ThumbModel();
        $thumbModel->userId = $id;
        $thumbModel->momentsId = $momentsId;
        $thumbModel->timestamp = time();
        $thumbModel->save();
    }


    /**
     * 评论朋友圈
     * @param  int    $id        [description]
     * @param  int    $momentsId [description]
     * @param  string $content   [description]
     * @return [type]            [description]
     */
    public static function comment(int $id, int $momentsId, string $content) {
        $commentsModel = new CommentsModel();
        $commentsModel->userId = $id;
        $commentsModel->momentsId = $momentsId;
        $commentsModel->content = $content;
        $commentsModel->timestamp = time();
        $commentsModel->save();
    }






}

?>