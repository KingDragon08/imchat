<?php 
namespace App\Services;

use App\Models\AdsModel;
use App\Models\GamesModel;
use App\Models\ConfigModel;
use App\Models\ChatRoomsModel;

use App\Services\EaseService;

use Storage;
use Exception;

class CommonService {

    /**
     * 获取广告
     * @return [type] [description]
     */
    public static function getAds() {
        return AdsModel::select('*')->where('status', 1)->orderBy('id', 'desc')->limit(10)->get()->toArray();
    }

    /**
     * 获取游戏
     * @return [type] [description]
     */
    public static function getGames() {
        return GamesModel::select('*')->where('status', 1)->orderBy('id', 'desc')->get()->toArray();
    }

    /**
     * 获取配置
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public static function getConfig($key) {
        $data = ConfigModel::select('value')->where('key', $key)->get()->toArray();
        if (!empty($data)) {
            return $data[0]['value'];
        }
        return "未找到配置项";
    }

    /**
     * 获取游戏房间
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public static function getGameRooms($type) {
        $data = ChatRoomsModel::select('*')->where('type', $type)->get()->toArray();
        if (empty($data)) {
            return $data;    
        }
        // 获取聊天室的详情
        $roomsInfo = EaseService::getRoomsInfo(array_column($data, 'roomId'));
        for($i=0; $i<count($data); $i++) {
            foreach ($roomsInfo as $room) {
                if ($room['id'] == $data[$i]['roomId']) {
                    $data[$i]['name'] = $room['name'];
                    $data[$i]['count'] = $room['affiliations_count'];
                    $data[$i]['affiliations'] = [];
                    foreach ($room['affiliations'] as $affiliation) {
                        $data[$i]['affiliations'][] = isset($affiliation['member']) ? $affiliation['member'] : $affiliation['owner'];
                    }
                }
            }
        }
        return $data;
    }

}

?>