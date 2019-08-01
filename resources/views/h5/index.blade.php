<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8" />
    <title>牛牛</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <link rel="stylesheet" href="../css/reset.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/swiper-3.4.1.min.css" />
    <link rel="stylesheet" href="../css/layout.css" />
    <link rel="stylesheet" href="../css/game.css" />
    <link rel="stylesheet" href="../css/home.css" />
    <link rel="stylesheet" href="../css/aui.css" />
    
    <script src="../js/jquery-1.9.1.min.js"></script>
    <script src="../js/zepto.min.js"></script>
    <script src="../js/fontSize.js"></script>
    <script src="../js/swiper-3.4.1.min.js"></script>
    <script src="../js/wcPop/wcPop.js"></script>
    <script src="../js/TouchSlide.1.1.js"></script>
    <script src="../js/util.js"></script>
    
    <style>
        .focus{ width:100%; height:150px;  margin:0 auto; position:relative; overflow:hidden;   }
        .focus .hd{ width:100%; height:5px;  position:absolute; z-index:1; bottom:0; text-align:center;  }
        .focus .hd ul{ overflow:hidden; display:-moz-box; display:-webkit-box; display:box; height:5px; background-color:rgba(51,51,51,0.5);   }
        .focus .hd ul li{ -moz-box-flex:1; -webkit-box-flex:1; box-flex:1; }
        .focus .hd ul .on{ background:#FF4000;  }
        .focus .bd{ position:relative; z-index:0; width: 100%;}
        .focus .bd a {display: block; height: 150px;}
        .focus .bd li {padding: 0;}
        .focus .bd li img{ width:100%;  height:150px; }
        .focus .bd li a{ -webkit-tap-highlight-color:rgba(0, 0, 0, 0);}
        .space {
            background-color: transparent !important; height: 1.5rem;
        }
        .aui-list-item {
            height: 1rem; border-bottom: 1px solid #eee !important;
        }
        .btn {
            width: 90%; margin: 14px auto; text-align: center; color: #fff;
            background: #ba8414; height: 1rem; line-height: 1rem;
            border-radius: 1rem; font-size: 16px;
        }
    </style>

</head>
<body>
    
    <!-- <>微聊主容器 -->
    <div class="wechat__panel clearfix">
        <div class="wc__home-wrapper flexbox flex__direction-column">
            <!-- //顶部 -->
            <div class="wc__headerBar fixed">
                <div class="inner flexbox">
                    <h2 class="barTit barTitLg flex1" id="title">俱乐部</h2>
                </div>
            </div>

            <!-- //4个tabBar滑动切换 -->
            <div class="wc__swiper-tabBar flex1">
                <div class="swiper-container">
                    <div class="swiper-wrapper">

                        <!-- //1、）俱乐部-->
                        <div class="swiper-slide">
                            <div class="wc__scrolling-panel">
                                <!-- //聊天记录信息 -->
                                <div class="wc__recordChat-list" id="J__recordChatList">
                                    <!-- 轮播图广告 -->
                                    <div id="focus" class="focus">
                                        <div class="hd">
                                            <ul></ul>
                                        </div>
                                        <div class="bd">
                                            <ul>
                                                @foreach ($ads as $ad)
                                                    <li onclick="javascript:location.href='{{$ad['url']}}'">
                                                        <img src="{{$ad['img']}}"/>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- 游戏列表 -->
                                    <div class="container" id="vue" style="margin-top: 1.2em;">
                                    @foreach ($games as $game)
                                        <div class="item" onclick="javascript:location.href='/h5/rooms?type={{$game['type']}}&name={{$game['name']}}'">
                                            <img src="{{$game['img']}}" alt="图片走丢了">
                                            <p>{{$game['name']}}</p>
                                        </div>
                                    @endforeach
                                </div>
                                </div>
                            </div>
                        </div>

                        <!-- //2、个人档案-->
                        <div class="swiper-slide">
                            <div class="wc__scrolling-panel">
                                <div class="wc__explore-list" id="J__exploreList">
                                    
                                    <div class="top">
                                        <img src="{{$userInfo['avatar']}}" onclick="changeAvatar()"/>
                                        <p class="name" onclick="changeInfo('nickname')" id="nickname">{{$userInfo['nickname']}}</p>
                                        <p class="time">{{Date('Y-m-d H:i:s' ,$userInfo['created_at'])}}</p>
                                    </div>
                                    <div class="jifen">
                                        <div class="left">
                                            <div style="flex: 1; padding: 10px;">
                                                <img src="../img/game/jifen.png">
                                            </div>
                                            <div style="flex: 3;">
                                                <p>积分</p>
                                                <p>{{round($userInfo['jifen'] * 1.0 / 100, 2)}}</p>
                                            </div>
                                        </div>
                                        <div class="right">
                                            <div style="flex: 3; text-align: right;">
                                                <p>红包积分</p>
                                                <p>{{round($userInfo['bonus'] * 1.0 / 100, 2)}}</p>
                                            </div>
                                            <div style="flex: 1; padding: 10px;">
                                                <img src="../img/game/bonus.png">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="list">
                                        <div class="item">
                                            <div class="avatar">
                                                <img src="../img/game/id.png">
                                            </div>
                                            <div class="content">
                                                <p class="title">登录ID</p>
                                                <p class="value">{{$userInfo['username']}}</p>
                                            </div>
                                        </div>

                                        <div class="item">
                                            <div class="avatar">
                                                <img src="../img/game/jieshaoren.png">
                                            </div>
                                            <div class="content">
                                                <p class="title">介绍人</p>
                                                <p class="value">{{$userInfo['agent'] ?? '-'}}</p>
                                            </div>
                                        </div>

                                        <div class="item">
                                            <div class="avatar">
                                                <img src="../img/game/phone.png">
                                            </div>
                                            <div class="content">
                                                <p class="title">电话号码</p>
                                                <p class="value" id="phone" onclick="changeInfo('phone')">{{$userInfo['phone'] ?? '-'}}</p>
                                            </div>
                                        </div>

                                        <div class="item">
                                            <div class="avatar">
                                                <img src="../img/game/mail.png">
                                            </div>
                                            <div class="content">
                                                <p class="title">电邮地址</p>
                                                <p class="value" id="email" onclick="changeInfo('email')">{{$userInfo['email'] ?? '-'}}</p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- //3、设置-->
                        <div class="swiper-slide">
                            <div class="wc__scrolling-panel">
                                <div class="wc__ucenter-list" id="J__ucenterList">
                                    
                                    <div class="aui-content aui-margin-b-15">
                                        <ul class="aui-list aui-list-in">
                                            <li class="aui-list-item" onclick="changePwd()">
                                                <div class="aui-list-item-inner">
                                                    更换密码
                                                </div>
                                            </li>
                                            <li class="aui-list-item" onclick="cservice()">
                                                <div class="aui-list-item-inner">
                                                    联系客服
                                                </div>
                                            </li>
                                            <li class="aui-list-item" onclick="chongzhi()">
                                                <div class="aui-list-item-inner">
                                                    充值
                                                </div>
                                            </li>
                                            <li class="aui-list-item" onclick="tixian()">
                                                <div class="aui-list-item-inner">
                                                    提现
                                                </div>
                                            </li>
                                            <!-- <li class="aui-list-item">
                                                <div class="aui-list-item-inner">
                                                    上传收款码
                                                </div>
                                            </li> -->
                                            <li class="aui-list-item">
                                                <div class="aui-list-item-inner">
                                                    版本
                                                    <div class="aui-list-item-right" style="font-size: 14px; color: #ba8414;">
                                                        0.0.1
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="btn" onclick="logout()">登出</div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- //底部tabbar -->
            <div class="wechat__tabBar">
                <div class="bottomfixed wc__borT">
                    <ul class="flexbox flex-alignc wechat-pagination">
                        <li class="flex1 on"><i class="ico i1"></i><span>俱乐部</span></li>
                        <li class="flex1"><i class="ico i2"></i><span>个人档案</span></li>
                        <li class="flex1"><i class="ico i3"></i><span>设置</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- 左右滑屏切换.Start -->
    <script type="text/javascript"> 
        // 整体页面滑动
        var chatSwiper = new Swiper('.swiper-container',{
            pagination: '.wechat-pagination',
            paginationClickable: true,
            paginationBulletRender: function (chatSwiper, index, className) {
                switch (index) {
                    case 0:
                        name='<i class="ico i1"></i><span>俱乐部</span>';
                        // $("#title").text('俱乐部');
                        break;
                    case 1:
                        name='<i class="ico i2"></i><span>个人档案</span>';
                        // $("#title").text('个人档案');
                        break;
                    case 2:
                        name='<i class="ico i3"></i><span>设置</span>';
                        // $("#title").text('设置');
                        break;
                    default: 
                        name='';
                }
                return '<li class="flex1 ' + className + '">' + name + '</li>';
            },
            onSlideChangeStart: function(swiper){
                $('#title').text(['俱乐部', '个人档案', '设置'][swiper.activeIndex]);
            }
        });
        // 轮播图
        TouchSlide({ 
            slideCell:"#focus",
            titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
            mainCell:".bd ul", 
            effect:"leftLoop", 
            autoPlay:true,//自动播放
            autoPage:true //自动分页
        });
    </script>
    <!-- 左右滑屏切换 end -->

    <script type="text/javascript">
        /** __公共函数 */
        $(function(){
            $(".wechat__panel").on("contextmenu", function(e){e.preventDefault();});
        });
        
        function changeInfo (key) {
            let map = {
                'nickname': '昵称',
                'phone': '电话号码',
                'email': '邮箱'
            };
            var value = prompt('输入' + map[key]);
            if (value && value.length) {
                $.ajax({
                    url: 'user/changeUserInfo',
                    method: 'PUT',
                    data: {
                        id: '{{$userInfo['id']}}',
                        token: '{{$userInfo['token']}}',
                        key: key,
                        value: value
                    },
                    success: function(e) {
                        if (e.status == 0) {
                            $('#' + key).text(value);
                        } else {
                            util.toast(e.msg);
                        }
                    },
                    error: function (e) {
                        util.toast('网络错误,退出重试');
                    }
                });
            }
        }

        // 更改密码
        function changePwd() {
            location.href = '/h5/changePwd';
        }

        // 客服
        function cservice() {
            location.href = '/h5/cfg?key=客服';
        }

        // 充值
        function chongzhi() {
            location.href = '/h5/cfg?key=充值';
        }

        // 提现
        function tixian() {
            location.href = '/h5/cfg?key=提现';
        }

        // 登出
        function logout() {
            if (confirm('确认登出?')) {
                location.href = '/h5/logout';   
            }
        }






    </script>
    
</body>
</html>
