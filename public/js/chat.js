$(function() {
    window.vue = new Vue({
        el: '#vue',
        data: {

        },
        mounted: function () {
            
        },
        beforeDestroy: function () {
            
        },
        methods: {

        },
    });
});

var sendRoomText = function (txt) {
    console.log(msg);
    var id = conn.getUniqueId();
    var msg = new WebIM.default.message('txt', id);
    var option = {
        msg: txt,
        to: api.pageParam.params.conversationId,
        roomType: true,
        ext: {},
        success: function () {
            console.log('send room text success');
        },
        fail: function () {
            console.log('failed');
        }
    };
    msg.set(option);
    msg.setGroup('groupchat');
    console.log(msg);
    conn.send(msg.body);
};