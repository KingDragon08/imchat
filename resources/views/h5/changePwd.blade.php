<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,initial-scale=1.0,width=device-width,height=device-height" />
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>更改密码</title>
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
    </style>
</head>

<body>
    <header class="aui-bar aui-bar-nav" id="header">
        <a class="aui-pull-left aui-btn" onclick="javascript: history.go(-1);">
            <span class="aui-iconfont aui-icon-left"></span>
        </a>
        <div class="aui-title" id="title">更换密码</div>
    </header>
    
    <div class="aui-content aui-margin-b-15" id="vue">
        <ul class="aui-list aui-form-list">
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        原密码
                    </div>
                    <div class="aui-list-item-input">
                        <input type="text" placeholder="原密码" v-model="pwd">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        新密码
                    </div>
                    <div class="aui-list-item-input">
                        <input type="text" placeholder="新密码" v-model="npwd">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        确认新密码
                    </div>
                    <div class="aui-list-item-input">
                        <input type="text" placeholder="确认新密码" v-model="cnpwd">
                    </div>
                </div>
            </li>
        </ul>
        <div class="aui-btn aui-btn-danger aui-btn-block aui-btn-sm" style="width:90%; margin: 20px auto; height: 2.25rem; line-height: 2.25rem;" @click="submit()">提交</div>
    </div>


</body>
<script src="../js/jquery-1.9.1.min.js"></script>
<script src="../js/util.js"></script>
<script src="../js/vue.min.js"></script>
<script type="text/javascript">
$(function() {
    var vue = new Vue({
            el: '#vue',
            data: {
                pwd: '',
                npwd: '',
                cnpwd: ''
            },
            methods: {
                submit: function () {
                    var that = this;
                    if (that.pwd.length<6 || that.npwd.length<6 || that.cnpwd.length<6 || that.npwd!=that.cnpwd) {
                        alert('输入有误');
                        return;
                    }
                    $.ajax({
                        url: '/user/changePwd',
                        method: 'put',
                        data: {
                            id: '{{$userInfo['id']}}',
                            token: '{{$userInfo['token']}}',
                            pwd: that.pwd,
                            npwd: that.npwd
                        },
                        success: function (e) {
                            if (e.status == 0) {
                                alert('更改成功,下次登录生效');
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
        });
});
</script>

</html>
