<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,initial-scale=1.0,width=device-width,height=device-height" />
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>登录</title>
    <link rel="stylesheet" type="text/css" href="../css/api.css" />
    <link rel="stylesheet" type="text/css" href="../css/aui.css" />
    <style>
    html {
        width: 100%; height: 100%;
        background-color: #f2f2f2; overflow: hidden;
    }
    body{
        overflow: hidden; background: none;
        width: 100%; height: 100%; margin: 0; padding: 0;
    }
    .logo{
        width: 100px; height: 100px; margin: 5rem auto;
        display: flex; justify-content: center; align-items: center;
    }
    .logo img {
        border-radius: 100%;
    }
    .login {
        position: absolute; z-index: 10;
        left: 5%; right: 5%; top: 42%;
    }
    .btn {
        margin-top: 20px; background: rgba(31, 48, 197, 0.7);
    }
    .register {
        text-align: left;
        font-size: 14px;
        color: #fff;
        margin-top: 14px;
    }
    .forget {
        color: #fff; font-size: 14px;
        text-align: right; margin-top: -20px;
    }
    #bgvid {
        position: fixed; right:0; bottom:0;
        min-width:100%; min-height:100%; width: auto;height:
        auto;z-index:-100; background-size: cover;
        background-image: url(../img/game/login_video.gif);
    }
    .aui-list-item:active {
        background: none !important;
    }
    ::-webkit-input-placeholder { /* WebKit browsers */
      color: #fff;
    }

    ::-moz-placeholder { /* Mozilla Firefox 19+ */
      color: #fff;
    }

    :-ms-input-placeholder { /* Internet Explorer 10+ */
      color: #fff;
    }
    </style>
</head>

<body>
    <!-- <video muted autoplay="autoplay" loop="loop" id="bgvid" src="../img/game/login_video.mp4" webkit-playsinline="true"></video> -->
    <div id="bgvid"></div>
    <div class="logo">
        <img src="../img/game/logo.png" width="120">
    </div>
    <div class="login">
        <ul class="aui-list aui-form-list" style="background: none;">
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label-icon">
                        <i class="aui-iconfont aui-icon-mobile" style="color: #fff;"></i>
                    </div>
                    <div class="aui-list-item-input">
                        <input type="text" placeholder="账号" id="username" style="color: #fff;" autocomplete="new-password">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label-icon">
                        <i class="aui-iconfont aui-icon-lock" style="color: white;"></i>
                    </div>
                    <div class="aui-list-item-input">
                        <input type="password" placeholder="密码" id="password" style="color: white;" autocomplete="new-password">
                    </div>
                </div>
            </li>
        </ul>
        <div class="aui-btn aui-btn-primary aui-btn-block aui-btn-sm btn" onclick="login()" tapmode>登录</div>
        <!-- <div class="register" onclick="register()" tapmode>注册账号</div>
        <div class="forget" onclick="forget()" tapmode>找回密码</div> -->
    </div>

</body>
<script src="../js/jquery-1.9.1.min.js"></script>
<script src="../js/wcPop/wcPop.js"></script>
<script src="../js/util.js"></script>
<script type="text/javascript">
    /**
     * 登录
     * @return {[type]} [description]
     */
    function login() {
        let username = $('#username').val();
        let password = $('#password').val();
        if (username.length < 6 || password.length <6) {
            util.toast('用户名和密码不得少于6位');
            return;
        }
        $.ajax({
            url: '/user/login',
            method: 'post',
            data: {
                username: username,
                password: password
            },
            success: function (data) {
                if (data.status == 0) {
                    location.href = '/h5';
                } else {
                    util.toast(data.msg);
                }
            },
            error: function (e) {
                api.toast({
                    msg: '网络错误',
                    duration: 2000,
                    location: 'bottom'
                });
            }
        });
    }


</script>

</html>
