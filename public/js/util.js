var util = {
    toast: function (msg) {
        var index = wcPop({
            anim: 'fadeIn',
            shade: true,
            content: msg,
            style: 'background: rgba(17,17,17,.7); color: #fff;',
            time: 3
        });
    },
    transTimestamp: function(timestamp) {
        let time = new Date(timestamp);
        let y = time.getFullYear();
        let m = time.getMonth() + 1;
        m = m < 10 ? '0' + m : m;
        let d = time.getDate();
        d = d < 10 ? '0' + d : d;
        let h = time.getHours();
        h = h < 10 ? '0' + h : h;
        let mm = time.getMinutes();
        mm = mm < 10 ? '0' + mm : mm;
        let s = time.getSeconds();
        s = s < 10 ? '0' + s : s;
        return ret = y + '-' + m + '-' + d + ' ' + h + ':' + mm + ':' + s;

    }
};