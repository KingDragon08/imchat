<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,initial-scale=1.0,width=device-width,height=device-height" />
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>玩法设置</title>
    <link rel="stylesheet" type="text/css" href="../../css/api.css" />
    <link rel="stylesheet" type="text/css" href="../../css/aui.css" />
    <style>
        input {
            text-indent: 1rem;
        }
        .aui-list-item-label {
            width: 55% !important;
        }
        .aui-list-item {
            border-bottom: 1px solid #eee !important;
        }
        .border {
            border: 1px solid #ddd !important;
        }
        .space {
            height: 10px; background: #f2f2f2;
        }
        #header {background: #2f3130; height: 3rem; line-height: 3rem; border-bottom: 1px solid #ffba00;}
        #header .aui-btn {height: 3rem; line-height: 3rem;}
    </style>
</head>

<body>
    <header class="aui-bar aui-bar-nav" id="header">
        <a class="aui-pull-left aui-btn" onclick="javascript: history.go(-1);">
            <span class="aui-iconfont aui-icon-left"></span>
        </a>
        <div class="aui-title" id="title">玩法设置</div>
        <div class="aui-pull-right aui-btn" tapmode="" onclick="config()">设置</div>
    </header>
    <div id="vue" class="aui-content aui-margin-b-15">
        <ul class="aui-list aui-form-list">
            <li class="aui-list-header">玩法设置</li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        玩法
                    </div>
                    <div class="aui-list-item-input">
                        <label><input class="aui-radio" type="radio" name="gameType" value="0" v-model="data.gameType" :checked="data.gameType == 0">角分</label>
                        <label><input class="aui-radio" type="radio" name="gameType" value="1" v-model="data.gameType" :checked="data.gameType == 1">元角分</label>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        上庄抽水
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="上庄抽水" v-model="data.shangzhuangchoushui">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        喝水
                    </div>
                    <div class="aui-list-item-input">
                        <label><input class="aui-radio" type="radio" name="heshui" value="0" v-model="data.heshui" :checked="data.heshui == 0">喝水</label>
                        <label><input class="aui-radio" type="radio" name="heshui" value="1" v-model="data.heshui" :checked="data.heshui == 1">不喝水</label>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        押注时长
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="押注时长" v-model="data.yazhushichang">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        最低标庄
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="最低标庄" v-model="data.zuidibiaozhuang">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        封顶
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="封顶" v-model="data.fengding">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        闲几点以下自杀
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="闲几点以下自杀" v-model="data.kill">
                    </div>
                </div>
            </li>





            <li class="aui-list-header">发包设置</li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        单人金额
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="单人金额" v-model="data.bonus">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        随机金额
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="随机金额" v-model="data.bonusRandom">
                    </div>
                </div>
            </li>

            <li class="aui-list-header">群费设置</li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        包费扣除方式
                    </div>
                    <div class="aui-list-item-input">
                        <label><input class="aui-radio" type="radio" name="bonusFee" value="no" v-model="data.bonusFee" :checked="data.bonusFee == 'group'">扣群主</label>
                        <label><input class="aui-radio" type="radio" name="bonusFee" value="banker" v-model="data.bonusFee" :checked="data.bonusFee == 'banker'">扣庄</label>
                        <label><input class="aui-radio" type="radio" name="bonusFee" value="every" v-model="data.bonusFee" :checked="data.bonusFee == 'every'">自认包费</label>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        服务费
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="服务费" v-model="data.serverFee">
                        <label><input class="aui-radio" type="radio" name="serverFeeType" value="bets" v-model="data.serverFeeType" :checked="data.serverFeeType == 'bets'">下注比例</label>
                        <label><input class="aui-radio" type="radio" name="serverFeeType" value="number" v-model="data.serverFeeType" :checked="data.serverFeeType == 'number'">固定值</label>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        庄抽水模式
                    </div>
                    <div class="aui-list-item-input">
                        <label><input class="aui-radio" type="radio" name="bankerChoushui" value="every" v-model="data.bankerChoushui" :checked="data.bankerChoushui == 'every'">把把抽</label>
                        <label><input class="aui-radio" type="radio" name="bankerChoushui" value="win" v-model="data.bankerChoushui" :checked="data.bankerChoushui == 'win'">庄赢抽</label>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        庄抽水
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="庄抽水比例" v-model="data.bankerChoushuiRate">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        闲抽水模式
                    </div>
                    <div class="aui-list-item-input">
                        <label><input class="aui-radio" type="radio" name="xianChoushui" value="every" v-model="data.xianChoushui" :checked="data.xianChoushui == 'every'">把把抽</label>
                        <label><input class="aui-radio" type="radio" name="xianChoushui" value="win" v-model="data.xianChoushui" :checked="data.xianChoushui == 'win'">闲赢抽</label>
                    </div>
                </div>
            </li>

            <li class="aui-list-header">同点规则</li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        同点规则
                    </div>
                    <div class="aui-list-item-input">
                        <label><input class="aui-radio" type="radio" name="tongdian" value="banker" v-model="data.tongdian" :checked="data.tongdian == 'banker'">庄赢</label>
                        <label><input class="aui-radio" type="radio" name="tongdian" value="xian" v-model="data.tongdian" :checked="data.tongdian == 'xian'">闲赢</label>
                        <label><input class="aui-radio" type="radio" name="tongdian" value="he" v-model="data.tongdian" :checked="data.tongdian == 'he'">打和</label>
                        <label><input class="aui-radio" type="radio" name="tongdian" value="bonus" v-model="data.tongdian" :checked="data.tongdian == 'bonus'">比金额</label>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        同点几点以下庄赢
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="同点几点以下庄赢" v-model="data.tongdianBankerWin">
                    </div>
                </div>
            </li>

            <li class="aui-list-header">超时设置</li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        超时时间（秒）
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="超时时间（秒）" v-model="data.overtime">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        庄超时设置
                    </div>
                    <!-- 0->认尾1, 1->认尾2, 2->认输, 3->大平小赔, 4->自动开包 -->
                    <div class="aui-list-item-input">
                        <label><input class="aui-radio" type="radio" name="bankerOvertime" value="0" v-model="data.bankerOvertime" :checked="data.bankerOvertime == 0">认尾1</label>
                        <label><input class="aui-radio" type="radio" name="bankerOvertime" value="1" v-model="data.bankerOvertime" :checked="data.bankerOvertime == 1">认尾2</label>
                        <label><input class="aui-radio" type="radio" name="bankerOvertime" value="2" v-model="data.bankerOvertime" :checked="data.bankerOvertime == 2">认输</label>
                        <label><input class="aui-radio" type="radio" name="bankerOvertime" value="3" v-model="data.bankerOvertime" :checked="data.bankerOvertime == 3">大平小赔</label>
                        <label><input class="aui-radio" type="radio" name="bankerOvertime" value="4" v-model="data.bankerOvertime" :checked="data.bankerOvertime == 4">自动开包</label>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        闲超时设置
                    </div>
                    <!-- 0->认尾1, 1->认尾2, 2->认输, 3->大平小赔, 4->自动开包 -->
                    <div class="aui-list-item-input">
                        <label><input class="aui-radio" type="radio" name="userOvertime" value="0" v-model="data.userOvertime" :checked="data.userOvertime == 0">认尾1</label>
                        <label><input class="aui-radio" type="radio" name="userOvertime" value="1" v-model="data.userOvertime" :checked="data.userOvertime == 1">认尾2</label>
                        <label><input class="aui-radio" type="radio" name="userOvertime" value="2" v-model="data.userOvertime" :checked="data.userOvertime == 2">认输</label>
                        <label><input class="aui-radio" type="radio" name="userOvertime" value="3" v-model="data.userOvertime" :checked="data.userOvertime == 3">大平小赔</label>
                        <label><input class="aui-radio" type="radio" name="userOvertime" value="4" v-model="data.userOvertime" :checked="data.userOvertime == 4">自动开包</label>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        庄闲同时超时设置
                    </div>
                    <!-- 0->打和, 1->庄赢, 2->闲赢, 3->自动开包 -->
                    <div class="aui-list-item-input">
                        <label><input class="aui-radio" type="radio" name="bothOvertime" value="0" v-model="data.bothOvertime" :checked="data.bothOvertime == 0">打和</label>
                        <label><input class="aui-radio" type="radio" name="bothOvertime" value="1" v-model="data.bothOvertime" :checked="data.bothOvertime == 1">庄赢</label>
                        <label><input class="aui-radio" type="radio" name="bothOvertime" value="2" v-model="data.bothOvertime" :checked="data.bothOvertime == 2">闲赢</label>
                        <label><input class="aui-radio" type="radio" name="bothOvertime" value="3" v-model="data.bothOvertime" :checked="data.bothOvertime == 3">自动开包</label>
                    </div>
                </div>
            </li>


            <li class="aui-list-header">压注设置</li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        最低押注
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="最低押注" v-model="data.minZhu">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        最高押注
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="最高押注" v-model="data.maxZhu">
                        <label><input class="aui-radio" type="radio" name="maxZhuType" value="banker" v-model="data.maxZhuType" :checked="data.maxZhuType == 'banker'">庄分比例</label>
                        <label><input class="aui-radio" type="radio" name="maxZhuType" value="number" v-model="data.maxZhuType" :checked="data.maxZhuType == 'number'">固定值</label>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        最高押注余额比例
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="最高押注余额比例" v-model="data.maxZhuRate">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        梭哈下注
                    </div>
                    <div class="aui-list-item-input">
                        <input type="checkbox" class="aui-switch" :checked="data.showHand" v-model="data.showHand">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        最低梭哈
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="最低梭哈" v-model="data.minShowHand">
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        最高梭哈
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="最高押注" v-model="data.maxShowHand">
                        <label><input class="aui-radio" type="radio" name="maxShowHandType" value="banker" v-model="data.maxShowHandType" :checked="data.maxShowHandType == 'banker'">庄分比例</label>
                        <label><input class="aui-radio" type="radio" name="maxShowHandType" value="number" v-model="data.maxShowHandType" :checked="data.maxShowHandType == 'number'">固定值</label>
                    </div>
                </div>
            </li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        最高梭哈余额比例
                    </div>
                    <div class="aui-list-item-input">
                        <input type="number" placeholder="最高梭哈余额比例" v-model="data.maxShowHandRate">
                    </div>
                </div>
            </li>


            <li class="aui-list-header">常规牌型设置</li>
            <section v-for="(item, index) in data.niuniu">
                <li class="aui-list-header">[[item.pai]]点</li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            名称
                        </div>
                        <div class="aui-list-item-input">
                            <input type="text" placeholder="名称" v-model="item.name">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            大小
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="大小" v-model="item.zIndex">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            抽水
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="抽水" v-model="item.fee">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            庄赢赔率
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="庄赢赔率" v-model="item.banker">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            闲赢赔率
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="闲赢赔率" v-model="item.user">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            梭哈赔率
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="梭哈赔率" v-model="item.showHand">
                        </div>
                    </div>
                </li>
            </section>


            <li class="aui-list-header">特殊牌型设置</li>
            <li class="aui-list-item">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-input">
                        <div class="aui-btn aui-btn-info aui-margin-r-5" @click='addRule(-1)'>添加</div>
                    </div>
                </div>
            </li>
            <section v-for="(item, index) in data.special">
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            名称
                        </div>
                        <div class="aui-list-item-input">
                            <input type="text" placeholder="名称" v-model="item.name">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            牌面
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="牌面" v-model="item.pai">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            大小
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="大小" v-model="item.zIndex">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            抽水
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="抽水" v-model="item.fee">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            庄赢赔率
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="庄赢赔率" v-model="item.banker">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            闲赢赔率
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="闲赢赔率" v-model="item.user">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            梭哈赔率
                        </div>
                        <div class="aui-list-item-input">
                            <input type="number" placeholder="梭哈赔率" v-model="item.showHand">
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label">
                            操作
                        </div>
                        <div class="aui-list-item-input">
                            <div class="aui-btn aui-btn-info aui-margin-r-5" @click='addRule(index)'>添加</div>
                            <div class="aui-btn aui-btn-danger aui-margin-r-5" @click='deleteRule(index)'>删除</div>
                        </div>
                    </div>
                </li>
                <div class="space">&nbsp;</div>
            </section>




        </ul>
    </div>
    <!-- UIListBadge -->
</body>
<script type="text/javascript" src="../../js/vue.min.js"></script>
<script type="text/javascript" src="../../js/jquery-1.9.1.min.js"></script>
<script src="../../js/wcPop/wcPop.js"></script>
<script type="text/javascript" src="../../js/util.js"></script>

<script type="text/javascript">
    let vue = '';

    $().ready(function() {
        init();
    });

    function init () {
        vue = new Vue({
            el: '#vue',
            delimiters:['[[',']]'],
            data: {
                data: {
                    gameType: 0, // 0->角分, 1->元角分
                    shangzhuangchoushui: 0.01, // 上庄抽水
                    hehsui: 0, // 喝水[0->喝水,输光不赔 1->不喝水 可以输成负数]
                    yazhushichang: 60, // 押注时长
                    zuidibiaozhuang: 10000, // 最低标庄
                    fengding: 200000, // 封顶
                    overtime: 30, // 超时时间（秒）
                    bankerOvertime: 0, // 庄超时处理: 0->认尾1, 1->认尾2, 2->认输, 3->大平小赔, 4->自动开包
                    userOvertime: 0, // 闲超时处理: 0->认尾1, 1->认尾2, 2->认输, 3->大平小赔, 4->自动开包
                    bothOvertime: 0, // 庄闲同时超时: 0->打和, 1->庄赢, 2->闲赢, 3->自动开包
                    bonus: 2, // 发包时单人的金额
                    bonusRandom: 2, // 总包的随机额度
                    bonusFee: 'banker', // 包费扣除方式 {group:扣群主, banker:自动扣庄,every:自认包费
                    serverFee: 38, //服务费
                    serverFeeType: 'number', // 服务费收取方式 {bets: 下注百分比, number: 固定值}
                    bankerChoushui: 'every', // 庄抽水规则 {every:把把抽, win:庄赢抽}
                    bankerChoushuiRate: 0.03, // 庄抽水比例
                    xianChoushui: 'every', // 闲抽水规则 {every:把把抽, win:闲赢抽}
                    tongdian: 'he', // 同点规则 {banker:庄赢, xian:闲赢, he:打和, bonus:比金额}
                    minZhu: 5, // 最小下注
                    maxZhu: 5, // 最高押注
                    maxZhuType: 'banker', // 最高注类型 {banker:按庄的比例, number:固定值}
                    maxZhuRate: 10, // 最高下注余额比例
                    showHand: true, // 开启梭哈下注
                    minShowHand: 5, // 最小梭哈
                    maxShowHand: 5, // 最高梭哈
                    maxShowHandType: 'banker', // 最高梭哈类型 {banker:按庄的比例, number:固定值}
                    maxShowHandRate: 10, // 最高梭哈余额比例
                    kill: 0, // 闲几点以下自杀
                    tongdianBankerWin: 0, // 同点几点以下庄赢
                    niuniu: [], // 牌型设置
                    special: [], // 特殊牌型设置
                }
            },
            mounted: function () {
                let that = this;
                this.getConfig(function (data) {
                    if (data == null || data == '') {
                        that.defaultSet();
                    } else {
                        that.data = data;
                    }
                });

            },
            methods: {
                addRule: function (index) {
                    let that = this;
                    that.data.special.splice(index+1, 0, {
                        name: '名称',
                        pai: '牌面',
                        zIndex: '大小,数值越大则牌型越大',
                        fee: '抽水',
                        banker: '庄赢赔率',
                        user: '闲赢赔率',
                        showHand: '梭哈赔率'
                    })
                },
                deleteRule: function (index) {
                    let that = this;
                    if (confirm('确认删除')) {
                        that.data.special.splice(index, 1)
                    }
                },
                defaultSet: function () {
                    let that = this;
                    that.data = {
                        gameType: 0, // 0->角分, 1->元角分
                        shangzhuangchoushui: 0.01, // 上庄抽水
                        hehsui: 0, // 喝水
                        yazhushichang: 60, // 押注时长
                        zuidibiaozhuang: 10000, // 最低标庄
                        fengding: 200000, // 封顶
                        overtime: 30, // 超时时间（秒）
                        bankerOvertime: 0, // 庄超时处理: 0->认尾1, 1->认尾2, 2->认输, 3->大平小赔, 4->自动开包
                        userOvertime: 0, // 闲超时处理: 0->认尾1, 1->认尾2, 2->认输, 3->大平小赔, 4->自动开包
                        bothOvertime: 0, // 庄闲同时超时: 0->打和, 1->庄赢, 2->闲赢, 3->自动开包
                        bonus: 2, // 发包时单人的金额
                        bonusRandom: 2, // 总包的随机额度
                        bonusFee: 'banker', // 包费扣除方式 {group:扣群主, banker:自动扣庄,every:自认包费
                        serverFee: 38, //服务费
                        serverFeeType: 'number', // 服务费收取方式 {bets: 下注百分比, number: 固定值}
                        bankerChoushui: 'every', // 庄抽水规则 {every:把把抽, win:庄赢抽}
                        bankerChoushuiRate: 0.03, // 庄抽水比例
                        xianChoushui: 'every', // 闲抽水规则 {every:把把抽, win:闲赢抽}
                        tongdian: 'he', // 同点规则 {banker:庄赢, xian:闲赢, he:打和, bonus:比金额}
                        minZhu: 5, // 最小下注
                        maxZhu: 5, // 最高押注
                        maxZhuType: 'banker', // 最高注类型 {banker:按庄的比例, number:固定值}
                        maxZhuRate: 10, // 最高下注余额比例
                        showHand: true, // 开启梭哈下注
                        minShowHand: 5, // 最小梭哈
                        maxShowHand: 5, // 最高梭哈
                        maxShowHandType: 'banker', // 最高梭哈类型 {banker:按庄的比例, number:固定值}
                        maxShowHandRate: 10, // 最高梭哈余额比例
                        kill: 0, // 闲几点以下自杀
                        tongdianBankerWin: 0, // 同点几点以下庄赢
                        niuniu: [], // 牌型设置
                        special: [], // 特殊牌型设置
                    }
                    that.defaultPai();
                },
                defaultPai: function () {
                    let that = this;
                    // 生成正常牌型
                    let i = 1;
                    for (; i<10; i++) {
                        that.data.niuniu.push({
                            name: '牛' + i, // 名称
                            pai: i, // 牌面
                            zIndex: i, // 大小,数值越大则牌型越大
                            fee: 0.03, // 抽水
                            banker: i, // 庄赢赔率
                            user: i, // 闲赢赔率
                            showHand: 1.0, // 梭哈赔率
                        });
                    }
                    that.data.niuniu.push({
                        name: '牛牛', // 名称
                        pai: 10, // 牌面
                        zIndex: 10, // 大小,数值越大则牌型越大
                        fee: 0.03, // 抽水
                        banker: 10, // 庄赢赔率
                        user: 10, // 闲赢赔率
                        showHand: 1.0, // 梭哈赔率
                    });
                    // 生成特殊牌型
                    // 金牛
                    let pais = ['0.10', '0.20', '0.30', '0.40', '0.50', '0.60', '0.70', '0.80', '0.90']; // 19
                    for (i=0; i<pais.length; i++) {
                        that.data.special.push({
                            name: '金牛-' + (i+1), // 名称
                            pai: pais[i], // 牌面
                            zIndex: 11 + i, // 大小,数值越大则牌型越大
                            fee: 0.03, // 抽水
                            banker: 11, // 庄赢赔率
                            user: 11, // 闲赢赔率
                            showHand: 1.0, // 梭哈赔率
                        });
                    }
                    // 对子
                    pais = ['0.11', '0.22', '0.33', '0.44', '0.55', '0.66', '0.77', '0.88', '0.99']; // 28
                    for (i=0; i<pais.length; i++) {
                        that.data.special.push({
                            name: '对子-' + (i+1), // 名称
                            pai: pais[i], // 牌面
                            zIndex: 20 + i, // 大小,数值越大则牌型越大
                            fee: 0.03, // 抽水
                            banker: 12, // 庄赢赔率
                            user: 12, // 闲赢赔率
                            showHand: 1.0, // 梭哈赔率
                        });
                    }
                    // 正顺
                    pais = ['1.23', '2.34', '3.45', '4.56', '5.67', '6.78', '7.89']; // 35
                    for (i=0; i<pais.length; i++) {
                        that.data.special.push({
                            name: '正顺-' + (i+1), // 名称
                            pai: pais[i], // 牌面
                            zIndex: 29 + i, // 大小,数值越大则牌型越大
                            fee: 0.03, // 抽水
                            banker: 13, // 庄赢赔率
                            user: 13, // 闲赢赔率
                            showHand: 1.0, // 梭哈赔率
                        });
                    }
                    // 倒顺
                    pais = ['3.21', '4.32', '5.43', '6.54', '7.65', '8.76', '9.87']; // 42
                    for (i=0; i<pais.length; i++) {
                        that.data.special.push({
                            name: '倒顺-' + (i+1), // 名称
                            pai: pais[i], // 牌面
                            zIndex: 36 + i, // 大小,数值越大则牌型越大
                            fee: 0.03, // 抽水
                            banker: 14, // 庄赢赔率
                            user: 14, // 闲赢赔率
                            showHand: 1.0, // 梭哈赔率
                        });
                    }
                    // 满牛
                    pais = ['1.00', '2.00', '3.00', '4.00', '5.00', '6.00', '7.00', '8.00', '9.00', '10.00']; // 52
                    for (i=0; i<pais.length; i++) {
                        that.data.special.push({
                            name: '满牛-' + (i+1), // 名称
                            pai: pais[i], // 牌面
                            zIndex: 43 + i, // 大小,数值越大则牌型越大
                            fee: 0.03, // 抽水
                            banker: 15, // 庄赢赔率
                            user: 15, // 闲赢赔率
                            showHand: 1.0, // 梭哈赔率
                        });
                    }
                    // 豹子
                    pais = ['1.11', '2.22', '3.33', '4.44', '5.55', '6.66', '7.77', '8.88', '9.99']; // 61
                    for (i=0; i<pais.length; i++) {
                        that.data.special.push({
                            name: '豹子-' + (i+1), // 名称
                            pai: pais[i], // 牌面
                            zIndex: 53 + i, // 大小,数值越大则牌型越大
                            fee: 0.03, // 抽水
                            banker: 16, // 庄赢赔率
                            user: 16, // 闲赢赔率
                            showHand: 1.0, // 梭哈赔率
                        });
                    }
                },
                getConfig: function (callback) {
                    $.ajax({
                        url: '/game/niuniu/config',
                        method: 'get',
                        data: {
                            id: '{{$userInfo['id']}}',
                            token: '{{$userInfo['token']}}',
                            roomId: '{{$roomId}}'
                        },
                        success: function (e) {
                            if (e.status == 0) {
                                if (callback) {
                                    callback(e.data);    
                                }
                            } else {
                                util.toast(e.msg);
                            }
                        },
                        error: function (e) {
                            util.toast('网络错误,退出重试');
                        }
                    });
                },
                setConfig: function (callback) {
                    let that = this;
                    $.ajax({
                        url: '/game/niuniu/config',
                        method: 'put',
                        data: {
                            id: '{{$userInfo['id']}}',
                            token: '{{$userInfo['token']}}',
                            roomId: '{{$roomId}}',
                            cfg: JSON.stringify(that.data)
                        },
                        success: function (e) {
                            if (e.status == 0) {
                                if (callback) {
                                    callback(e.data);    
                                }
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
    }

    function config() {
        vue.setConfig(function () {
            // history.go(-1);
        });
    }


</script>

</html>
