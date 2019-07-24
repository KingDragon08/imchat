<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{$title}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <link rel="stylesheet" href="../css/reset.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/swiper-3.4.1.min.css" />
    <link rel="stylesheet" href="../css/layout.css" />
    <link rel="stylesheet" href="../css/weui.css" />
    <link rel="stylesheet" href="../css/bonus.css" />
    <script src="../js/jquery-1.9.1.min.js"></script>
    <script src="../js/zepto.min.js"></script>
    <script src="../js/fontSize.js"></script>
    <script src="../js/swiper-3.4.1.min.js"></script>
    <script src="../js/wcPop/wcPop.js"></script>
    <script src="../js/weui.min.js"></script>
    <script src="../js/vue.min.js"></script>
    <script src="../js/chat.js"></script>
    <script src="../js/webim/WebIMConfig.js"></script>
    <script src="../js/webim/webimSDK.js"></script>
    <script src="../js/util.js"></script>

    <style>
        .swiper-slide {background: none !important;}
        [v-cloak] {display: none;}
        .bonus {
            width: 220px; height: 86px; line-height: 70px;
            color: #fff; font-size: 16px; text-align: center; padding: 0 14px;
            border-radius: 5px; overflow: hidden; white-space: nowrap;
            text-overflow: ellipsis; background: #FFA73A; margin-left: 0.2rem;
        }
        .bonus img {
            width: 50px; float: left; margin-top: 10px;
        }
        .bonus div {
            float: left;
        }
        .bonus-footer {
            background: #fff; color: #ccc; text-align: left; text-indent: 1em;
            height: 16px; line-height: 16px; width: 220px;
            margin-left: -14px;
        }
        .popui__panel-cnt {
            max-height: 500px; overflow: scroll;
        }
    </style>
</head>

<body>
    <div class="wechat__panel clearfix">
        <div class="wc__chat-wrapper flexbox flex__direction-column">
            <!-- //顶部 -->
            <div class="wc__headerBar fixed">
                <div class="inner flexbox">
                    <a class="back splitline" href="javascript:;" onclick="history.back(-1);"></a>
                    <h2 class="barTit flex1" id="title" style="text-align: left; margin-left: 0.25rem;">{{$title}}</h2>
                    <a class="barIco u-qun" href="javascript:;"></a>
                </div>
            </div>
            <!-- //微聊消息上墙面板 -->
            <div class="wc__chatMsg-panel flex1" id="vue" v-cloak>
                <div class="chatMsg-cnt">
                    <ul class="clearfix" id="J__chatMsgList">
                        <section v-for="(message, index) in messages">
                            <!-- 别人-->
                            <li class="others" v-if="message.from != userInfo.username">
                                <a class="avatar" href="javascript:;">
                                    <img :src="'/common/avatar/' + message.from" />
                                </a>
                                <div class="content">
                                    <p class="author">[[message.from]]</p>
                                    <!-- 纯文本 -->
                                    <div class="msg" v-html="message.data" v-if="!message.ext.hasOwnProperty('type')"></div>
                                    <!-- 游戏表情 -->
                                    <div class="msg picture" v-if="message.ext.hasOwnProperty('type') && message.ext.type == 'niuniu_emotion'">
                                        <img class="img__pic" style="width:2.5rem;" :src="'https://imchat.bj.bcebos.com/DD/' + message.ext.url" />
                                    </div>
                                    <!-- 普通表情 -->
                                    <div class="msg lgface" v-if="message.ext.hasOwnProperty('type') && message.ext.type == 'emotion'" v-html="message.ext.url"></div>
                                    <!-- 红包 -->
                                    <div class="bonus"
                                        @click.stop="openBonus(message, index)"
                                        v-if="message.ext.hasOwnProperty('type') && message.ext.hasOwnProperty('niuniu') && message.ext.type == 'bonus'"
                                    >
                                        <img src="../img/game/bonus1.png">
                                        <div>[[message.ext.ext]]</div>
                                        <div class="bonus-footer">牛牛红包</div>
                                    </div>
                                    
                                </div>
                            </li>
                            <!--自己-->
                            <li class="me" v-if="message.from == userInfo.username">
                                <div class="content">
                                    <p class="author">[[message.from]]</p>
                                    <!-- 纯文本 -->
                                    <div class="msg" v-html="message.data" v-if="!message.ext.hasOwnProperty('type')"></div>
                                    <!-- 游戏表情 -->
                                    <div class="msg picture" v-if="message.ext.hasOwnProperty('type') && message.ext.type == 'niuniu_emotion'">
                                        <img class="img__pic" style="width:2.5rem;" :src="'https://imchat.bj.bcebos.com/DD/' + message.ext.url" />
                                    </div>
                                    <!-- 普通表情 -->
                                    <div class="msg lgface" v-if="message.ext.hasOwnProperty('type') && message.ext.type == 'emotion'" v-html="message.ext.url"></div>
                                    <!-- 红包 -->
                                    <div class="bonus"
                                        style="float: right; margin-right: 0.2rem;" 
                                        @click.stop="openBonus(message, index)"
                                        v-if="message.ext.hasOwnProperty('type') && message.ext.hasOwnProperty('niuniu') && message.ext.type == 'bonus'"
                                    >
                                        <img src="../img/game/bonus1.png">
                                        <div>[[message.ext.ext]]</div>
                                        <div class="bonus-footer">牛牛红包</div>
                                    </div>
                                </div>
                                <a class="avatar" href="javascript:;">
                                    <img :src="'/common/avatar/' + message.from" />
                                </a>
                            </li>
                        </section>
                    </ul>
                </div>

                <div id="J__popupTmpl-Users" style="display:none;">
                    <div class="wc__popupTmpl tmpl-hongbao">
                        <i class="wc-xclose" onclick="wcPop.close()"></i>
                        <div class="weui-cells__title">选择庄家</div>
                        <div class="weui-cells weui-cells_radio">
                            <label class="weui-cell weui-check__label" :id="'label' + index" :for="'x' + index" v-for="(user, index) in members" @click.stop="selectZhuang(index)">
                                <div class="weui-cell__bd">
                                    <p>[[user.username]]</p>
                                </div>
                                <div class="weui-cell__ft">
                                    <input type="radio" class="weui-check" name="radio" :id="'x' + index" :value="user" v-model="zhuang">
                                    <span class="weui-icon-checked"></span>
                                </div>
                            </label>
                        </div>

                        <div class="weui-cells">
                            <div class="weui-cell">
                                <div class="weui-cell__bd">
                                    <input class="weui-input" type="number" placeholder="上庄积分" id="shangzhuangJifen">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="bonus-bg" v-show="bonus.show">
                    <div class="bonus-ct">
                        <div class="l1">
                            <img :src="bonus.avatar">
                            <span>[[bonus.name]]的红包</span>
                        </div>
                        <div class="l2">[[bonus.msg]]</div>
                        <div class="border">
                            <div class="hider"></div>
                            <div class="open" @click="bonusGo()"></div>
                        </div>
                    </div>
                    <div class="close" @click="closeBonus()">
                        <i class="aui-iconfont aui-icon-close">X</i>
                    </div>
                </div>



            </div>
    
            @include('h5.panel')

        </div>
    </div>


    <!-- …… 图片预览弹窗.Start -->
    <div class="wc__popup-imgPreview" style="display: none;">
        <div class="swiper-container J__swiperImgPreview"><div class="swiper-wrapper"></div>
            <!-- <div class="swiper-pagination pagination-imgPreview"></div> -->
        </div>
    </div>
    <script type="text/javascript">
        var curIndex = 0, imgPreviewSwiper;
        $("body").on("click", "#J__chatMsgList li .picture", function(){
            var html = "",  _src = $(this).find("img").attr("src");
            $("#J__chatMsgList li .picture").each(function(i, item){
                html += '<div class="swiper-slide"><div class="swiper-zoom-container">'+ $(this).html() +'</div></div>';
                if($(this).find("img").attr("src") == _src){curIndex = i;
                }
            });
            $(".J__swiperImgPreview .swiper-wrapper").html(html);
            $(".wc__popup-imgPreview").show();
            imgPreviewSwiper = new Swiper('.J__swiperImgPreview',{
                pagination: false,paginationClickable: true,zoom: true,observer: true,observeParents: true,initialSlide: curIndex
            });
        });
        // 关闭预览
        $(".wc__popup-imgPreview").on("click", function(e){
            var that = $(this);imgPreviewSwiper.destroy(true, true);$(".J__swiperImgPreview .swiper-wrapper").html('');that.hide();
        });
    </script>
    <!-- …… 图片预览弹窗.End -->

    <!-- …… 表情模板.Start -->
    <!-- //表情 -->
    @include('h5.emotion')
    <!-- …… 表情模板.End -->
    
    <script type="text/javascript">
        /** __公共函数 */
        $(function(){
            // 禁止长按弹出系统菜单
            $(".wechat__panel").on("contextmenu", function(e){e.preventDefault();});
        });
        
        /** __自定函数 */
        $(function(){
            

            // ...点击聊天面板区域
            $(document).on("click", ".wc__chatMsg-panel", function(e){
                var _tapMenu = $(".wc__chatTapMenu");
                if(_tapMenu.length && e.target != _tapMenu && !$.contains(_tapMenu[0], e.target)){
                    // 关闭长按菜单
                    _tapMenu.hide();$(".wc__chatMsg-panel").find("li .msg").removeClass("taped");
                }$(".wc__choose-panel").hide();
            });

            // ...表情、选择区切换
            $(".wc__editor-panel").on("click", ".btn", function(){var that = $(this);
                $(".wc__choose-panel").show();if (that.hasClass("btn-emotion")) {
                    $(".wc__choose-panel .wrap-emotion").show();$(".wc__choose-panel .wrap-choose").hide();
                    // 初始化swiper表情
                    !emotionSwiper && $("#J__emotionFootTab ul li.cur").trigger("click");
                } else if (that.hasClass("btn-choose")) {
                    $(".wc__choose-panel .wrap-emotion").hide();$(".wc__choose-panel .wrap-choose").show();
                }wchat_ToBottom();
            });

            // ...处理编辑器信息
            var $editor = $(".J__wcEditor"), _editor = $editor[0];
            function surrounds(){
                setTimeout(function () { //chrome
                    var sel = window.getSelection();var anchorNode = sel.anchorNode;
                    if (!anchorNode) return;if (sel.anchorNode === _editor ||
                        (sel.anchorNode.nodeType === 3 && sel.anchorNode.parentNode === _editor)) {
                        var range = sel.getRangeAt(0);var p = document.createElement("p");
                        range.surroundContents(p);
                        range.selectNodeContents(p);range.insertNode(document.createElement("br")); //chrome
                        sel.collapse(p, 0);
                        (function clearBr() {
                            var elems = [].slice.call(_editor.children);for (var i = 0, len = elems.length; i < len; i++) {
                                var el = elems[i];if (el.tagName.toLowerCase() == "br") {_editor.removeChild(el);
                                }
                            }elems.length = 0;
                        })();
                    }
                }, 10);
            }
            // 格式化编辑器包含标签
            _editor.addEventListener("click", function () {$(".wc__choose-panel").hide();
            }, true);
            _editor.addEventListener("focus", function(){surrounds();
            }, true);
            _editor.addEventListener("input", function(){surrounds();
            }, false);
            // 点击表情
            $("#J__swiperEmotion").on("click", ".face-list span img", function(){
                var that = $(this), range;
                if(that.hasClass("face")){ //小表情
                    var img = that[0].cloneNode(true);_editor.focus();
                    _editor.blur(); //输入表情时禁止输入法
                    setTimeout(function(){
                        if(document.selection && document.selection.createRange){document.selection.createRange().pasteHTML(img);
                        }else if(window.getSelection && window.getSelection().getRangeAt){range = window.getSelection().getRangeAt(0);range.insertNode(img);
                            range.collapse(false);var sel = window.getSelection();sel.removeAllRanges();sel.addRange(range);
                        }
                    }, 10);
                }else if(that.hasClass("del")){ //删除
                    _editor.focus();_editor.blur(); //输入表情时禁止输入法
                    setTimeout(function(){
                        range = window.getSelection().getRangeAt(0);range.collapse(false);
                        var sel = window.getSelection();sel.removeAllRanges();sel.addRange(range);document.execCommand("delete");
                    }, 10);
                } else if(that.hasClass("lg-face")){ //大表情
                    var _img = that.parent().html();
                    console.log(_img);
                    sendEmotion(_img, 'emotion');
                }
            });
            // 发送信息
            var $chatMsgList = $("#J__chatMsgList");
            function isEmpty(){
                var html = $editor.html();html = html.replace(/<br[\s\/]{0,2}>/ig, "\r\n");
                html = html.replace(/<[^img].*?>/ig, "");html = html.replace(/&nbsp;/ig, "");
                return html.replace(/\r\n|\n|\r/, "").replace(/(?:^[ \t\n\r]+)|(?:[ \t\n\r]+$)/g, "") == "";
            }
            $(".J__wchatSubmit").on("click", function(){
                // 判断内容是否为空
                if(isEmpty()) return;
                var html = $editor.html();
                var reg = /(http:\/\/|https:\/\/)((\w|=|\?|\.|\/|&|-)+)/g;
                html = html.replace(reg, "<a href='$1$2'>$1$2</a>");
                sendText(html);
                wchat_ToBottom();
                // 清空聊天框并获取焦点（处理输入法和表情 - 聚焦）
                if(!$(".wc__choose-panel").is(":hidden")){$editor.html("");
                }else{$editor.html("").focus().trigger("click");
                }wchat_ToBottom();
            });
            
            // ...长按弹出菜单
            $("#J__chatMsgList").on("longTap", "li .msg", function(e){
                var that = $(this), menuTpl, menuNode = $("<div class='wc__chatTapMenu animated anim-fadeIn'></div>");that.addClass("taped");
                that.parents("li").siblings().find(".msg").removeClass("taped");
                var isRevoke = that.parents("li").hasClass("me");var _revoke = isRevoke ? "<a href='#'><i class='ico i4'></i>撤回</a>" : "";
                if(that.hasClass("picture")){
                    menuTpl = "<div class='menu menu-picture'><a href='#'><i class='ico i1'></i>复制</a><a href='#'><i class='ico i2'></i>收藏</a><a href='#'><i class='ico i3'></i>另存为</a>"+ _revoke +"<a href='#'><i class='ico i5'></i>删除</a></div>";
                }else if(that.hasClass("video")){
                    menuTpl = "<div class='menu menu-video'><a href='#'><i class='ico i3'></i>另存为</a>" + _revoke +"<a href='#'><i class='ico i5'></i>删除</a></div>";
                }else{
                    menuTpl = "<div class='menu menu-text'><a href='#'><i class='ico i1'></i>复制</a><a href='#'><i class='ico i2'></i>收藏</a>" + _revoke +"<a href='#'><i class='ico i5'></i>删除</a></div>";
                }
                if(!$(".wc__chatTapMenu").length){
                    $(".wc__chatMsg-panel").append(menuNode.html(menuTpl));
                    autoPos();
                }else{
                    $(".wc__chatTapMenu").hide().html(menuTpl).fadeIn(250);
                    autoPos();
                }
                function autoPos(){
                    var _other = that.parents("li").hasClass("others");
                    $(".wc__chatTapMenu").css({position: "absolute",left: that.position().left + parseInt(that.css("marginLeft")) + (_other ? 0 : that.outerWidth() - $(".wc__chatTapMenu").outerWidth()),top: that.position().top - $(".wc__chatTapMenu").outerHeight() - 8
                    });
                }
            });
            // ...销毁长按弹窗
            $(".wc__chatMsg-panel").on("scroll", function(){
                $(".wc__chatTapMenu").hide();$(this).find("li .msg").removeClass("taped");
            });
        });
    </script>
    
</body>
<script src="../js/webim/my.js"></script>
<script type="text/javascript">
var userInfo = {!! json_encode($userInfo) !!};
var api = {
    pageParam: {
        roomInfo: JSON.parse(localStorage.getItem('{{$conversationId}}')),
        params: {
            conversationId: '{{$conversationId}}',
            chatType: 'chatRoom',
            title: '{{$title}}'
        }
    }
};

$(function () {
    $("#title").text($("#title").text() + '(' + api.pageParam.roomInfo.members.length + ')');
    initChat();
    // 登录
    options = {
        apiUrl: WebIM.config.apiURL,
        user: userInfo.username,
        pwd: '123456',
        appKey: WebIM.config.appkey
    };
    conn.open(options);
});



</script>
</html>