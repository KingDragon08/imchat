var util = {
    toast: function (msg) {
        var index = wcPop({
            anim: 'fadeIn',
            shade: true,
            content: msg,
            style: 'background: rgba(17,17,17,.7); color: #fff;',
            time: 3
        });
    }
};