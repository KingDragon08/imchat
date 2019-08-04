window.host = 'http://127.0.0.1/E6w0xqoz0KWeURfVbH8gff3G8CUgq+gip0sr6ClqoSc=/';
window.hrefs = window.location.href.split('/');
window.href = hrefs[hrefs.length - 1];
if (href == '' || href == 'E6w0xqoz0KWeURfVbH8gff3G8CUgq+gip0sr6ClqoSc=') {
    href = 'new';
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
                key: 'new',
                icon: 'mail',
                title: '新开比赛',
                link: window.host + 'new'
            },
            {
                key: 'bets',
                icon: 'clock-circle-o',
                title: '下注记录',
                link: window.host + 'bets'
            },
            {
                key: 'user',
                icon: 'user',
                title: '用户积分',
                link: window.host + 'user'
            },
            {
                key: 'jifen',
                icon: 'smile-o',
                title: '上分记录',
                link: window.host + 'jifen'
            },
            {
                key: 'history',
                icon: 'solution',
                title: '历史比赛',
                link: window.host + 'history'
            }
        ]
    }
}