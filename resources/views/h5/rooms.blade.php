<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,initial-scale=1.0,width=device-width,height=device-height" />
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="../css/reset.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/swiper-3.4.1.min.css" />
    <link rel="stylesheet" href="../css/layout.css" />
    <link rel="stylesheet" href="../css/game.css" />
    <link rel="stylesheet" href="../css/home.css" />
    <link rel="stylesheet" href="../css/aui.css" />
    <style>
    #header {background: #2f3130; height: 3rem; line-height: 3rem; border-bottom: 1px solid #ffba00;}
    #header .aui-btn {height: 3rem; line-height: 3rem;}
    .aui-list-item {
        height: 3rem; border-bottom: 1px solid #eee !important;
    }
    html,body {
        width: 100%; height: 100%;
    }
    .bg {
        position: fixed; z-index: -1;
        left: 0; right: 0; top: 0; bottom: 0;
        background-image: url('../img/game/bg.png');
        background-size: cover; background-position: center center; background-repeat: no-repeat;
    }
    .item {
        margin-bottom: 0 !important;
    }
    .item img {
        border-radius: 50% !important; border: 10px inset #f2ab18;
    }
    .item span {
        width: 90%; height: 2.5rem; text-align: center; left: 5%;
        position: absolute; top: calc(40% - 1.25rem); color: #f2ab18; display: block;
        text-overflow: ellipsis; overflow: hidden;
    }
    .item span div:first-child {
        height: 1.5rem; line-height: 1.5rem; font-size: 18px; font-weight: bold;
    }
    .item span div:last-child {
        height: 1rem; line-height: 1rem; font-size: 14px;
    }
    .popui__panel-cnt {
        text-align: left !important;
    }
    </style>
</head>

<body>
    <header class="aui-bar aui-bar-nav" id="header">
        <a class="aui-pull-left aui-btn" onclick="javascript: history.go(-1);">
            <span class="aui-iconfont aui-icon-left"></span>
        </a>
        <div class="aui-title" id="title">{{$title}}</div>
    </header>
    <div class="bg"></div>
    <div style="height:1rem;">&nbsp;</div>
    <div class="container">
        @foreach ($rooms as $index => $room)
        <div class="item" onclick="startGame({{$index}})">
            <img src="{{$room['avatar']}}">
            <span>
                <div>{{$room['name']}}</div>
                <div>{{$room['count']}}人在线</div>
            </span>
        </div>
        @endforeach
    </div>
</body>
<script src="../js/jquery-1.9.1.min.js"></script>
<script src="../js/util.js"></script>
<script src="../js/wcPop/wcPop.js"></script>
<script type="text/javascript">
var rooms = {!! json_encode($rooms) !!};
var userInfo = {!! json_encode($userInfo) !!};

function startGame(index) {
    var room = rooms[index];
    // 如果不是聊天室的成员则先加入聊天室
    if (room.affiliations.indexOf(userInfo.username) == -1) {
        $.ajax({
            url: '/ease/joinChatRoom',
            method: 'post',
            data: {
                id: userInfo.id,
                token: userInfo.token,
                roomId: room.roomId,
                username: userInfo.username
            },
            success: function(e) {
                if (e.status == 0) {
                    goGame(room);
                } else {
                    util.toast(e.msg);
                }
            },
            error: function(e) {
                util.toast('网络错误,退出重试');
            }
        });
    } else {
        goGame(room);
    }
}

// 进入聊天界面
function goGame(room) {
    // 获取房间详情
    $.ajax({
        url: '/ease/roomInfo',
        method: 'post',
        data: {
            id: userInfo.id,
            token: userInfo.token,
            roomId: room.roomId
        },
        success: function(e) {
            localStorage.setItem(room.roomId, JSON.stringify(e.data));
            if (room.rules) {
                var index = wcPop({
                    title: '规则说明',
                    content: room.rules,
                    shadeClose: false,
                    anim: 'fadeIn',
                    xclose: true,
                    btns: [
                        {
                            text: '确定',
                            style: 'color: #333',
                            onTap() {
                                wcPop.close(index);
                                location.href = '/h5/chat?id=' + room.roomId + '&name=' + room.name;                
                            }
                        }
                    ]
                });
            } else {
                location.href = '/h5/chat?id=' + room.roomId + '&name=' + room.name;
            }
        },
        error: function(e) {
            util.toast('网络错误,退出重试');
        }
    });
}
</script>

</html>