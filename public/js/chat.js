$(function() {
    window.vue = new Vue({
        el: '#vue',
        delimiters:['[[',']]'],
        data: {
            messages: [],
            isOwner: false,
            owner: api.pageParam.roomInfo.owner.username,
            firstLoad: true,
            emotionData: [],
            conversationId: api.pageParam.params.conversationId,
            chatType: api.pageParam.params.chatType,
            roomInfo: api.pageParam.roomInfo,
            members: api.pageParam.roomInfo.members,
            gaming: false, // 是否游戏中
            gameId: -1, // 当前游戏的id
            userInfo: userInfo,
            gameStartText: '######开始下注######', // 开始游戏的提示语
            gameEndText: '######停止下注######', // 游戏结束的提示语
            bonus: {
                show: false,
                name: '',
                avatar: '',
                msg: '',
                message: '',  // 消息原始数据
            },
            zhuang: -1, // 庄的username
            joiners: 0, // 参与者个人
        },
        mounted: function () {
            let that = this;
            if (that.userInfo.username == that.owner) {
                that.isOwner = true;
                $('#btn-choose').show();
                $('#swiper__tmpl-emotion00').show();
            }
            let flag = false;
            for (let i=0; i<that.roomInfo.admin.length; i++) {
                if (that.roomInfo.admin[i]['username'] == that.userInfo.username) {
                    flag = true;
                    break;
                }
            }
            // 管理员开放图片权限
            if (flag) {
                $('#swiper__tmpl-emotion00').show();
            }
        },
        methods: {
            openBonus: function (message, index) {
                //console.log(message);
                // this.bonus.show = true;
                this.bonus.name = message.from;
                this.bonus.avatar = '/common/avatar/' + message.from;
                this.bonus.msg = '恭喜发财,大吉大利';
                this.bonus.message = message;
                this.bonus.index = index;
                this.joiners = -1;
                var ttt = this;
                $('#bonus_name').text(message.from);
                $('#bonus_result_name').text(message.from);
                $('#bonus_avatar').attr('src', 'http://via.placeholder.com/200/ffa93b/ffffff?text=' + message.from);
                // $('#bonus_result_avatar').attr('src', 'http://via.placeholder.com/200/ffa93b/ffffff?text=' + message.from);
                $('#bonus_result_avatar').text(message.from[0]);
                var getHbIdx = wcPop({
                    id: 'wdtPopGetHb',
                    skin: 'ios',
                    content: $("#J__popupTmpl-getRedPacket").html(),
                    xclose: true,
                    style: 'background-color: #f3f3f3; width: 300px;',
                    show: function() {
                        $("body").on("click", ".J__btnGetRedPacket", function() {
                            var that = $(this);
                            that.addClass("active");
                            setTimeout(function (){
                                $.ajax({
                                    url: '/game/niuniu/openBonus',
                                    data: {
                                        bonusId: message.ext.id,
                                        roomId: message.to,
                                        id: ttt.userInfo.id,
                                        token: ttt.userInfo.token
                                    },
                                    method: 'post',
                                    success: function (data) {
                                        var joiner = data.data.joiner;
                                        ttt.joiners = joiner.length;
                                        $('#bonus_result_list').html('');
                                        var html = '';
                                        for (let i = 0; i < joiner.length; i++) {
                                            html += '<li>' +
                                                        '<a class="wcim__material-cell flexbox flex-alignc" href="#">' +
                                                            '<span class="avator">' +
                                                                // '<img src="http://via.placeholder.com/200/2f3130/ffffff?text=' + joiner[i]['username'] + '">' +
                                                                '<div class="div_avatar">' + joiner[i]['username'][0] + '</div>' + 
                                                            '</span>' +
                                                            '<label class="flex1 flexbox flex-alignc">' +
                                                                '<span class="flex1">' +
                                                                    '<em class="db fs-30">' + joiner[i]['username'] + '</em>' +
                                                                    '<em class="db fs-24 c-9ea0a3 rmt-5">' + util.transTimestamp(joiner[i]['timestamp'] * 1000) + '</em>' +
                                                                '</span>' +
                                                                '<em class="moneyNum">' + (joiner[i]['amount'] / 100).toFixed(2) + '元</em>' +
                                                            '</label>' +
                                                        '</a>' +
                                                    '</li>';
                                            if (joiner[i]['id'] == ttt.userInfo.id) {
                                                $('#bonus_result_total').text((joiner[i]['amount'] / 100).toFixed(2));
                                            }
                                        }
                                        $('#bonus_result_list').html(html);
                                        that.removeClass("active");
                                        wcPop.close(getHbIdx);
                                        var viewHbIdx = wcPop({
                                            id: 'wcim_hb_fullscreen',
                                            title: '红包详情',
                                            skin: 'fullscreen',
                                            content: $("#J__popupTmpl-viewRedPacket").html(),
                                            position: 'top',
                                            xclose: true,
                                            style: 'background: #f3f3f3;'
                                        });
                                        setTimeout(function(){
                                            ttt.refreshBonusResult(message.ext.id, message.to, 10);
                                        },100);
                                    }
                                });
                            }, 500);
                        });
                    }
                });
            },
            refreshBonusResult: function (bonusId, roomId, counter) {
                var that = this;
                if (counter > 0) {
                    $.ajax({
                        url: '/game/niuniu/openBonus',
                        data: {
                            bonusId: bonusId,
                            roomId: roomId,
                            id: userInfo.id,
                            token: userInfo.token
                        },
                        method: 'post',
                        success: function (data) {
                            var joiner = data.data.joiner;
                            if (joiner.length != that.joiners) {
                                that.joiners = joiner.length;
                                var html = '';
                                $('#wcim_hb_fullscreen #bonus_result_list').html(html);
                                for (let i = 0; i < joiner.length; i++) {
                                    html += '<li>' +
                                                '<a class="wcim__material-cell flexbox flex-alignc" href="#">' +
                                                    '<span class="avator">' +
                                                        // '<img src="http://via.placeholder.com/200/2f3130/ffffff?text=' + joiner[i]['username'] + '">' +
                                                        '<div class="div_avatar">' + joiner[i]['username'][0] + '</div>' + 
                                                    '</span>' +
                                                    '<label class="flex1 flexbox flex-alignc">' +
                                                        '<span class="flex1">' +
                                                            '<em class="db fs-30">' + joiner[i]['username'] + '</em>' +
                                                            '<em class="db fs-24 c-9ea0a3 rmt-5">' + util.transTimestamp(joiner[i]['timestamp'] * 1000) + '</em>' +
                                                        '</span>' +
                                                        '<em class="moneyNum">' + (joiner[i]['amount'] / 100).toFixed(2) + '元</em>' +
                                                    '</label>' +
                                                '</a>' +
                                            '</li>';
                                    if (joiner[i]['id'] == userInfo.id) {
                                        $('#wcim_hb_fullscreen #bonus_result_total').text((joiner[i]['amount'] / 100).toFixed(2));
                                    }
                                }
                                $('#wcim_hb_fullscreen #bonus_result_list').html(html);    
                            }
                            var start = (new Date()).getTime();
                            var delay = 500;
                            while ((new Date()).getTime() - start < delay) {
                                continue;
                            }
                            that.refreshBonusResult(bonusId, roomId, counter - 1);    
                            
                        }
                    });
                }
            },
            closeBonus: function () {
                this.bonus.show = false;
            },
            bonusGo: function () {
                if (this.bonus.message.ext.id) {
                    location.href = '/h5/result/' + api.pageParam.params.conversationId + '/' 
                                    + this.bonus.message.ext.id
                                    + '?message=' + JSON.stringify(this.bonus.message)
                } else {
                    this.closeBonus();
                    util.toast('数据错误');
                }
            },

            bet: function (type, number) {
                let that = this;
                // 下注时自动收起软键盘
                document.activeElement.blur();
                $.ajax({
                    url: '/game/niuniu/bet',
                    method: 'post',
                    data: {
                        id: userInfo.id,
                        token: userInfo.token,
                        roomId: api.pageParam.params.conversationId,
                        bet: number,
                        type: type
                    },
                    success: function(e) {
                        if (e.status == 0) {
                            util.toast(e.data);
                        } else {
                            util.toast(e.msg);
                        }
                    },
                    error: function(e) {
                        util.toast('网络错误,退出重试');
                    }
                });
            },
            selectZhuang: function(index) {
                $('#wcPop .weui-check__label').each(function (i) {
                    $(this).css('background', '#fff');
                });
                $('#wcPop #label' + index).css('background', 'red');
            },
            startGame: function () {
                var that = this;
                // 获取聊天室的成员信息
                $.ajax({
                    url: '/ease/roomInfo',
                    method: 'post',
                    data: {
                        id: userInfo.id,
                        token: userInfo.token,
                        roomId: api.pageParam.params.conversationId
                    },
                    success: function(e) {
                        if (e.status == 0) {
                            var data = e.data;
                            that.roomInfo = data;
                            that.members = that.roomInfo.members;
                            // 渲染结束后选庄
                            setTimeout(function () {
                                var bpidx = wcPop({
                                    skin: 'ios',
                                    content: $("#J__popupTmpl-Users").html(),
                                    style: 'background-color: #f0f0f0; max-width: 320px; width: 90%; max-height: 90%; overflow: hidden;',
                                    shadeClose: true,
                                    btns: [
                                        {
                                            text: '开始游戏',
                                            style: 'background:#2f3130;color:#fff;font-size:18px;',
                                            onTap() {
                                                // console.log(that.zhuang);
                                                var shangzhuangJifen = $('#wcPop #shangzhuangJifen').val();
                                                // console.log(shangzhuangJifen);
                                                if (that.zhuang != -1 && shangzhuangJifen >= 0 && parseInt(shangzhuangJifen) == shangzhuangJifen) {
                                                    // 后台上庄开始
                                                    $.ajax({
                                                        url: '/game/niuniu/create',
                                                        method: 'post',
                                                        data: {
                                                            id: userInfo.id,
                                                            token: userInfo.token,
                                                            roomId: api.pageParam.params.conversationId,
                                                            banker: that.zhuang.username,
                                                            jifen: shangzhuangJifen
                                                        },
                                                        success: function(e) {
                                                            if (e.status == 0) {
                                                                var data = e.data;
                                                                that.gameId = data.id;
                                                                that.gaming = true;
                                                                // 发送开始下注的文字
                                                                sendText(that.gameStartText + '<br/>' + data.msg + '<br/>')
                                                                // 发送开始下注的图片
                                                                setTimeout(function () {
                                                                    sendEmotion('3.gif');
                                                                }, 100);
                                                            } else {
                                                                alert(e.msg);
                                                            }
                                                        },
                                                        error: function(e) {
                                                            alert('网络错误,退出重试');
                                                        }
                                                    });
                                                } else {
                                                    alert('数据不合法');
                                                }
                                                wcPop.close(bpidx);
                                            }
                                        }
                                    ]
                                });
                            }, 100);

                        } else {
                            util.toast(e.msg);
                        }
                    },
                    error: function(e) {
                        util.toast('网络错误,退出重试');
                    }
                });
            }
        },
    });
});

var sendText = function (txt) {
    var id = conn.getUniqueId();
    var msg = new WebIM.default.message('txt', id);
    var option = {
        msg: txt,
        to: api.pageParam.params.conversationId,
        roomType: true,
        ext: {},
        success: function () {
            console.log('send text success');
            msg.body.data = txt;
            msg.body.from = userInfo.username;
            vue.$data.messages.push(msg.body);
            setTimeout(function(){
                wchat_ToBottom();
            }, 100);
            msg = txt.substr(3, txt.length-7);
            // 下注
            if (msg.substr(0, 2) == "梭哈" || msg.substr(0, 2).toUpperCase() == 'SH') {
                var tmp = msg.substr(2, msg.length-2).trim();
                // 检测到梭哈下注
                if (parseInt(tmp) == tmp) {
                    vue.bet('showHand', parseInt(tmp));
                }
            }
            // 普通下注
            if (parseInt(msg) == msg) {
                vue.bet('normal', parseInt(msg));
            }

            // 重推
            if (msg == '本局重推' && vue.$data.isOwner) {
                $.ajax({
                    url: '/game/niuniu/reset',
                    method: 'post',
                    data: {
                        id: userInfo.id,
                        token: userInfo.token,
                        roomId: api.pageParam.params.conversationId
                    },
                    success: function(e) {
                        if (e.status == 0) {
                            util.toast(e.data);
                        } else {
                            util.toast(e.msg);
                        }
                    },
                    error: function(e) {
                        util.toast('网络错误,退出重试');
                    }
                });
            }
        },
        fail: function (e) {
            console.log(e);
            console.log('send text failed');
        }
    };
    msg.set(option);
    msg.setGroup('groupchat');
    conn.send(msg.body);
};

var sendEmotion = function (url, type) {
    type = type || 'niuniu_emotion';
    var id = conn.getUniqueId();
    var msg = new WebIM.default.message('txt', id);
    var option = {
        msg: '表情',
        to: api.pageParam.params.conversationId,
        roomType: true,
        ext: {
            type: type,
            url: url
        },
        success: function () {
            console.log('send emotion success');
            msg.body.data = '表情';
            msg.body.from = userInfo.username;
            vue.$data.messages.push(msg.body);
            setTimeout(function(){
                wchat_ToBottom();
            }, 100);
        },
        fail: function () {
            console.log('send emotion failed');
        },
        onError: function (e) {
            console.log(e);
        }
    };
    msg.set(option);
    msg.setGroup('groupchat');
    conn.send(msg.body);
}

var imSendBonus = function (bonusId, amount, number) {
    var id = conn.getUniqueId();
    var msg = new WebIM.default.message('txt', id);
    var option = {
        msg: '红包',
        to: api.pageParam.params.conversationId,
        roomType: true,
        ext: {
            id: bonusId + '',
            amount: amount + '',
            number: number + '',
            ext: 'imchat红包',
            type: 'bonus',
            niuniu: '1',
        },
        success: function () {
            console.log('send bonus success');
            msg.body.data = 'imchat红包';
            msg.body.from = userInfo.username;
            vue.$data.messages.push(msg.body);
            setTimeout(function(){
                wchat_ToBottom();
            }, 100);
        },
        fail: function () {
            console.log('send emotion failed');
        },
        onError: function (e) {
            console.log(e);
        }
    };
    msg.set(option);
    msg.setGroup('groupchat');
    conn.send(msg.body);
}

var sendBonus = function () {
    $.ajax({
        url: '/game/niuniu/sendBonus',
        method: 'post',
        data: {
            id: userInfo.id,
            token: userInfo.token,
            roomId: api.pageParam.params.conversationId
        },
        success: function(e) {
            if (e.status == 0) {
                var data = e.data;
                imSendBonus(data.bonusId, data.amount, data.number);
            } else {
                util.toast(e.msg);
            }
        },
        error: function(e) {
            util.toast('网络错误,退出重试');
        }
    });
}

var startGame = function () {
    vue.startGame();
}

var stopGame = function () {
    $.ajax({
        url: '/game/niuniu/end',
        method: 'post',
        data: {
            id: userInfo.id,
            token: userInfo.token,
            roomId: api.pageParam.params.conversationId
        },
        success: function(e) {
            if (e.status == 0) {
                var data = e.data;
                // 发送注单
                sendText(data);
                setTimeout(function () {
                    // 发送停止下注的图片
                    sendEmotion('4.gif');    
                }, 100);
            } else {
                util.toast(e.msg);
            }
        },
        error: function(e) {
            util.toast('网络错误,退出重试');
        }
    });
}

var configGame = function () {
    location.href = '/h5/config/' + api.pageParam.params.conversationId
}

// 发送账单
var sendResult = function () {
    $.ajax({
        url: '/game/niuniu/result',
        method: 'post',
        data: {
            id: userInfo.id,
            token: userInfo.token,
            roomId: api.pageParam.params.conversationId
        },
        success: function(e) {
            if (e.status == 0) {
                var data = e.data;
                // 发送账单
                sendText(data);
                // 发送富豪榜
                $.ajax({
                    url: '/game/niuniu/bang',
                    method: 'post',
                    data: {
                        id: userInfo.id,
                        token: userInfo.token,
                        roomId: api.pageParam.params.conversationId
                    },
                    success: function(e) {
                        if (e.status == 0) {
                            var data = e.data;
                            // 发送富豪榜
                            sendText(data);
                        } else {
                            util.toast(e.msg);
                        }
                    },
                    error: function(e) {
                        util.toast('网络错误,退出重试');
                    }
                });
            } else {
                util.toast(e.msg);
            }
        },
        error: function(e) {
            util.toast('网络错误,退出重试');
        }
    });
}













