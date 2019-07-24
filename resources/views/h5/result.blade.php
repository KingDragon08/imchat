<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,initial-scale=1.0,width=device-width,height=device-height" />
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>niuniu result</title>
    <link rel="stylesheet" type="text/css" href="../../../css/api.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/aui.css" />
    <style>
        .container {
            color: #ffe4b4;
            padding: 10px;
            background: #e0604d;
        }

        .l1 {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .l1 img {
            display: inline;
            width: 4rem;
            height: 4rem;
            margin-right: 5px;
            border-radius: 2px;
            margin-bottom: 1rem;
        }

        .l2 {
            text-align: center;
            font-size: 18px;
            word-break: break-all;
            height: 20px;
            line-height: 20px;
        }

        .l3 {
            text-align: center;
            margin-top: 30px;
        }

        .l3 span {
            font-size: 72px;
            line-height: 80px;
            height: 80px;
            font-weight: bold;
        }

        .l4 {
            text-align: center;
            font-size: 12px;
            height: 60px;
            line-height: 60px;
        }

        .l5 {
            height: 8px;
            background: #f2f2f2;
        }
    </style>
</head>

<body>
    <div id="vue" class="aui-content aui-margin-b-15" v-cloak>
        <div class="container" style="">
            <div class="l1">
                <img :src="avatar" alt="">
            </div>
            <div class="l2">
                <span>[[name]]的红包</span>
            </div>
            <div class="l2">[[message.ext.ext]]</div>
            <div class="l3" v-if="amount != ''">
                <span>[[(amount / 100).toFixed(2)]]</span>元
            </div>
        </div>
        <div class="l5"></div>

        
        <div class="aui-content aui-margin-b-15">
            <ul class="aui-list aui-media-list">
                <li class="aui-list-item" v-for="item in joiner">
                    <div class="aui-media-list-item-inner">
                        <div class="aui-list-item-media">
                            <img :src="item.avatar">
                        </div>
                        <div class="aui-list-item-inner" style="border-bottom: 1px solid #f2f2f2;">
                            <div class="aui-list-item-text" style="height: 100%;">
                                <div class="aui-list-item-title">
                                    <div>[[item.username]]</div>
                                    <div style="font-size: 24px;">[[(item.amount / 100).toFixed(2)]]</div>
                                </div>
                                <div class="aui-list-item-right">[[util.transTimestamp(item.timestamp * 1000)]]</div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>



    </div>
    
</body>
<script type="text/javascript" src="../../../js/vue.min.js"></script>
<script type="text/javascript" src="../../../js/jquery-1.9.1.min.js"></script>
<script src="../../../js/wcPop/wcPop.js"></script>
<script type="text/javascript" src="../../../js/util.js"></script>
<script type="text/javascript">
    let vue = '';
    var userInfo = {!! json_encode($userInfo) !!};
    var message = {!! $_GET['message'] !!};
    $(function() {
        vue = new Vue({
            el: '#vue',
            delimiters:['[[',']]'],
            data: {
                name: '',
                avatar: '',
                opened: false,
                amount: '',
                bonusId: '{{$bonusId}}',
                roomId: '{{$roomId}}',
                message: message,
                joiner: [],
                interval: 0, // 循环器
                listData: [], // 红包结果数据
            },
            mounted: function() {
                this.init();
                // this.startInterval();
            },
            beforeDestroy: function() {
                clearInterval(this.interval);
            },
            methods: {
                getListData: function() {
                    let that = this;
                    window.Ajax({
                        url: '/game/niuniu/openBonus',
                        params: {
                            bonusId: that.bonusId,
                            roomId: that.roomId,
                        },
                        method: 'post'
                    }, function(data) {
                        that.opened = data.opened;
                        that.joiner = data.joiner;
                        var joiner = that.joiner;
                        for (let i = 0; i < joiner.length; i++) {
                            if (joiner[i]['id'] == userInfo.id) {
                                that.amount = joiner[i]['amount'];
                            }
                        }
                    });
                },
                startInterval: function() {
                    let that = this;
                    this.interval = setInterval(function() {
                        that.getListData();
                    }, 1200);
                },
                init: function() {
                    let that = this;
                    that.avatar = '/common/avatar/' + that.message.from;
                    that.name = that.message.from;
                    that.getListData();
                }
            }
        });
    });

    window.Ajax = function(params, callback) {
        let data = params.params;
        data.token = userInfo.token;
        data.id = userInfo.id;
        $.ajax({
            url: params.url,
            method: params.method || 'GET',
            data: data,
            success: function(e) {
                if (e.status == 0) {
                    callback(e.data);
                } else {
                    console.log(JSON.stringify(params));
                    console.log(JSON.stringify(e));
                    util.toast(e.msg);
                }
            },
            error: function(e) {
                console.log(JSON.stringify(params));
                console.log(JSON.stringify(e));
                util.toast('网络错误');
            }
        });
    }




</script>

</html>
