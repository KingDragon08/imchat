<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/uf/theme.min.css" />
        <title>历史游戏</title>
    </head>
    <body>
        <div id="uf"></div>

    </body>
    <script src="/js/uf/dll.min.js"></script>
    <script src="/js/uf/antd.min.js"></script>
    <script src="/js/uf/uf.min.js"></script>
    <script src="/js/admin/header.js"></script>
    <script src="/js/admin/footer.js"></script>
    <script src="/js/admin/nav.js"></script>
    <script src="/js/util.js"></script>
    <script>
        window.userInfo = {!! json_encode($userInfo) !!};
        header.content = header.content + userInfo.username + ' - ' + userInfo.role;

        var config = {
            type: 'layout',
            content: [
                header,
                {
                    type: 'content',
                    content: {
                        type: 'layout',
                        content: [
                            nav,
                            {
                                type: 'card',
                                style: {background: '#ffffff', color: '#333', minHeight: window.innerHeight - 164, width: '100%'},
                                content: {
                                    type: 'table',
                                    name: 'game-table',
                                    bordered: true,
                                    pagination: {
                                        pageSize: 10,
                                        pageType: 'client'
                                    },
                                    title: {
                                        basicWidget: [
                                            'setPageSize',
                                            'export',
                                            'switchTags',
                                            'fullScreen'
                                        ],
                                        extra: [
                                            {
                                                type: 'select',
                                                name: 'roomId',
                                                style: {
                                                    width: '200px'
                                                },
                                                placeholder: '按房间查询',
                                                source: {
                                                    url: '/common/rooms',
                                                    params: {
                                                        type: 'niuniu'
                                                    },
                                                    handler: function (data) {
                                                        var options = [];
                                                        for (var i=0; i<data.length; i++) {
                                                            options.push({
                                                                label: data[i]['name'],
                                                                value: data[i]['roomId']
                                                            });
                                                        }
                                                        return options;
                                                    }
                                                },
                                                onSelect: function (val) {
                                                    UF('game-table').set({
                                                        params: {
                                                            roomId: val
                                                        }
                                                    });
                                                }
                                            },
                                            {
                                                type: 'button',
                                                mode: 'primary',
                                                content: '重置',
                                                style: {
                                                    marginLeft: '14px'
                                                },
                                                onClick: function () {
                                                    UF('game-table').set({
                                                        params: {}
                                                    });
                                                }
                                            }
                                        ]
                                    },
                                    columns: [
                                        {
                                            title: 'ID',
                                            dataIndex: 'id'
                                        },
                                        {
                                            title: '房间ID',
                                            dataIndex: 'roomId'
                                        },
                                        {
                                            title: '红包ID',
                                            dataIndex: 'bonusId'
                                        },
                                        {
                                            title: '庄',
                                            dataIndex: 'banker'
                                        },
                                        {
                                            title: '状态',
                                            dataIndex: 'status',
                                            enum: [
                                                {
                                                    key: 0,
                                                    value: '已结束'
                                                },
                                                {
                                                    key: 1,
                                                    value: '进行中'
                                                },
                                                {
                                                    key: -1,
                                                    value: '重推作废'
                                                }
                                            ]
                                        },
                                        {
                                            title: '下注记录',
                                            dataIndex: 'joiners',
                                            textType: 'json',
                                            ellipsis: true
                                        },
                                        {
                                            title: '游戏结果',
                                            dataIndex: 'result',
                                            textType: 'json',
                                            ellipsis: true
                                        },
                                        {
                                            title: '时间',
                                            dataIndex: 'timestamp'
                                        }
                                    ],
                                    source: '/admin/gameList'
                                }
                            }
                        ]
                    }
                },
                footer
            ]
        };
        UF.init(config, '#uf');
    </script>
</html>
