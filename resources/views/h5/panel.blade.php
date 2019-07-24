<!-- //微聊底部功能面板 -->
<div class="wc__footTool-panel">
    <!-- 输入框模块 -->
    <div class="wc__editor-panel wc__borT flexbox">
        <div class="wrap-editor flex1">
            <div class="editor J__wcEditor" contenteditable="true" style="user-select:text;-webkit-user-select:text;"></div>
        </div>
        <i class="btn btn-emotion"></i>
        <i class="btn btn-choose" id="btn-choose" style="display: none;"></i>
        <button class="btn-submit J__wchatSubmit">发送</button>
    </div>
    <!-- 表情、选择模块 -->
    <div class="wc__choose-panel wc__borT" style="display: none;">
        <!-- 表情区域 -->
        <div class="wrap-emotion" style="display: none;">
            <div class="emotion__cells flexbox flex__direction-column">
                <div class="emotion__cells-swiper flex1" id="J__swiperEmotion">
                    <div class="swiper-container">
                        <div class="swiper-wrapper"></div>
                        <div class="pagination-emotion"></div>
                    </div>
                </div>
                <div class="emotion__cells-footer" id="J__emotionFootTab">
                    <ul class="clearfix">
                        <li class="swiperTmpl cur" tmpl="swiper__tmpl-emotion00" id="swiper__tmpl-emotion00" style="display: none;"><img src="../img/game/niuniu.png" alt=""></li>
                        <li class="swiperTmpl cur" tmpl="swiper__tmpl-emotion01"><img src="../img/emotion/face01/face-lbl.png" alt=""></li>
                        <li class="swiperTmpl" tmpl="swiper__tmpl-emotion02"><img src="../img/emotion/face02/face-lbl.gif" alt=""></li>
                        <li class="swiperTmpl" tmpl="swiper__tmpl-emotion03"><img src="../img/emotion/face03/face-lbl.gif" alt=""></li>
                        <li class="swiperTmpl" tmpl="swiper__tmpl-emotion04"><img src="../img/emotion/face04/face-lbl.gif" alt=""></li>
                        <li class="swiperTmpl" tmpl="swiper__tmpl-emotion05"><img src="../img/emotion/face05/face-lbl.gif" alt=""></li>
                        <li class="swiperTmpl" tmpl="swiper__tmpl-emotion06"><img src="../img/emotion/face06/face-lbl.gif" alt=""></li>
                        <!-- <li class="swiperTmplSet"><img src="../img/wchat/icon__emotion-set.png" alt=""></li> -->
                    </ul>
                </div>
            </div>
        </div>
        <!-- 选择区域 -->
        <div class="wrap-choose" style="display: none;">
            <div class="choose__cells">
                <ul class="clearfix">
                    <li>
                        <a class="J__wchatBp" href="javascript:;" onclick="sendBonus()">
                            <span class="img">
                                <img src="../img/game/niuniu.png" />
                            </span>
                            <em>发送红包</em>
                        </a>
                    </li>
                    <li>
                        <a class="J__wchatBp" href="javascript:;" onclick="startGame()">
                            <span class="img">
                                <img src="../img/game/niuniu.png" />
                            </span>
                            <em>开始下注</em>
                        </a>
                    </li>
                    <li>
                        <a class="J__wchatBp" href="javascript:;" onclick="stopGame()">
                            <span class="img">
                                <img src="../img/game/niuniu.png" />
                            </span>
                            <em>停止下注</em>
                        </a>
                    </li>
                    <li>
                        <a class="J__wchatBp" href="javascript:;" onclick="configGame()">
                            <span class="img">
                                <img src="../img/game/niuniu.png" />
                            </span>
                            <em>规则配置</em>
                        </a>
                    </li>
                    <li>
                        <a class="J__wchatBp" href="javascript:;" onclick="sendResult()">
                            <span class="img">
                                <img src="../img/game/niuniu.png" />
                            </span>
                            <em>发送账单</em>
                        </a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>
</div>