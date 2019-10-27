window.host = 'http://161.117.197.145/admin/';
// window.host = 'http://127.0.0.1/admin/';
window.hrefs = window.location.href.split('/');
window.href = hrefs[hrefs.length - 1];
if (href == '') {
    href = 'user';
}

window.nav = {
    type: 'sider',
    style: {color: '#fff', lineHeight: '120px'},
    content: {
        type: 'menu',
        name: 'my-menu',
        mode: 'inline',
        theme: 'dark',
        defaultSelectedKeys: href,
        items: [
            {
                key: 'user',
                icon: 'user',
                title: '用户列表',
                link: window.host + 'user'
            },
            {
                key: 'room',
                icon: 'bars',
                title: '房间列表',
                link: window.host + 'room'
            },
            {
                key: 'game',
                icon: 'clock-circle-o',
                title: '历史游戏',
                link: window.host + 'game'
            },
            {
                key: 'admin',
                icon: 'smile-o',
                title: '管理员',
                link: window.host + 'admin'
            },
            // {
            //     key: 'agent',
            //     icon: 'star-o',
            //     title: '代理列表',
            //     link: window.host + 'agent'
            // },
            // {
            //     key: 'bet',
            //     icon: 'heart-o',
            //     title: '下注记录',
            //     link: window.host + 'bet'
            // },
        ]
    }
}
