<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,initial-scale=1.0,width=device-width,height=device-height" />
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>历史数据</title>
    <link rel="stylesheet" href="../../css/aui.css" />
    <style>
    body,html {margin: 0; padding: 0;}
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
        <div class="aui-title" id="title">历史数据</div>
    </header>
    
    <div class="aui-content aui-margin-b-15">
        <ul class="aui-list aui-media-list">
            @foreach ($data as $index => $item)
            <li class="aui-list-item aui-list-item-arrow" onclick="detail('{{$item['id']}}')">
                <div class="aui-media-list-item-inner">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-text">
                            <div class="aui-list-item-title">第【{{$item['id']}}】局</div>
                            <div class="aui-list-item-right">{{$item['timestamp']}}</div>
                        </div>
                        <div class="aui-list-item-text aui-ellipsis-2">
                            庄【{{$item['banker']}}】
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

</body>
<script>
    function detail(id) {
        location.href = '/h5/hisDetail/' + '{{$roomId}}' + '/' + id;
    }
</script>
</html>
