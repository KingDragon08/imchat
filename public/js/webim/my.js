function initChat() {
    window.conn = {};
    WebIM.config = config;
    console.log(WebIM)
    conn = WebIM.conn = new WebIM.default.connection({
        appKey: WebIM.config.appkey,
        isHttpDNS: WebIM.config.isHttpDNS,
        isMultiLoginSessions: WebIM.config.isMultiLoginSessions,
        https: WebIM.config.https,
        url: WebIM.config.xmppURL,
        apiUrl: WebIM.config.apiURL,
        isAutoLogin: true,
        heartBeatWait: WebIM.config.heartBeatWait,
        autoReconnectNumMax: WebIM.config.autoReconnectNumMax,
        autoReconnectInterval: WebIM.config.autoReconnectInterval,
        isStropheLog: WebIM.config.isStropheLog,
        delivery: WebIM.config.delivery
    });

    conn.listen({
        onOpened: function(message) { //连接成功回调
            // 如果isAutoLogin设置为false，那么必须手动设置上线，否则无法收消息
            // 手动上线指的是调用conn.setPresence(); 如果conn初始化时已将isAutoLogin设置为true
            // 则无需调用conn.setPresence();
            console.log('onOpened');
            // 拉取历史消息
            conn.fetchHistoryMessages({
                queue: api.pageParam.params.conversationId,
                isGroup: true,
                count: 50,
                success: function (messages) {
                    console.log(messages);
                    vue.$data.messages = messages;
                    setTimeout(function(){
                        wchat_ToBottom();
                    }, 100);
                },
                faile: function (e) {
                    console.log(e);
                }
            });
        },
        onClosed: function(message) {
            console.log(message);
        }, 
        //收到文本消息
        onTextMessage: function(message) {
            console.log(message);
            if (message.to == api.pageParam.params.conversationId) {
                vue.$data.messages.push(message);
                setTimeout(function(){
                    wchat_ToBottom();
                }, 100);
            }
        }, 
        //收到图片消息
        onPictureMessage: function(message) {
            console.log(message);
        }, 
        //本机网络连接成功
        onOnline: function() {
            console.log('onOnline');
        }, 
        //本机网络掉线
        onOffline: function() {}, 
        //失败回调
        onError: function(message) {
            console.log(message);
            history.go(0);
        }
    });
}

// ...滚动聊天区底部
function wchat_ToBottom(){setTimeout(function(){
        $(".wc__chatMsg-panel").animate({scrollTop: $("#J__chatMsgList").height()}, 0);
    }, 10);
}



