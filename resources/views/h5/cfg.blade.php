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
    </style>
</head>

<body>
    <header class="aui-bar aui-bar-nav" id="header">
        <a class="aui-pull-left aui-btn" onclick="javascript: history.go(-1);">
            <span class="aui-iconfont aui-icon-left"></span>
        </a>
        <div class="aui-title" id="title">{{$title}}</div>
    </header>
    
    <div class="aui-content aui-margin-b-15" id="vue">
        {!!$data!!}
    </div>


</body>
<script src="../js/jquery-1.9.1.min.js"></script>
<script src="../js/util.js"></script>
</html>
