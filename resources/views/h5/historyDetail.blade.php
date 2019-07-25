<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,initial-scale=1.0,width=device-width,height=device-height" />
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>历史详情</title>
    <link rel="stylesheet" href="../../../css/aui.css" />
    <style>
    body,html {margin: 0; padding: 0; background: #f2f2f2;}
    #header {background: #2f3130; height: 3rem; line-height: 3rem; border-bottom: 1px solid #ffba00;}
    #header .aui-btn {height: 3rem; line-height: 3rem;}
    .aui-list {
        padding: 0;
    }
    </style>
</head>

<body>
    <header class="aui-bar aui-bar-nav" id="header">
        <a class="aui-pull-left aui-btn" onclick="javascript: history.go(-1);">
            <span class="aui-iconfont aui-icon-left"></span>
        </a>
        <div class="aui-title" id="title">历史详情</div>
    </header>

    <section class="aui-content-padded">
        <div class="aui-card-list">
            <div class="aui-card-list-header">
                第【{{$data['id']}}】局结果
            </div>
            <div class="aui-card-list-content-padded">
                庄【{{$data['banker']}}】
            </div>
            <div class="aui-card-list-footer">
                {{$data['timestamp']}}
            </div>
        </div>
    </section>

    <section class="aui-content-padded">
        <div class="aui-card-list">
            <div class="aui-card-list-header">
                账单
            </div>
            <div class="aui-card-list-content-padded">
                {!! $data['result'] !!}
            </div>
        </div>
    </section>

</body>
</html>
