<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/uf/theme.min.css" />
        <title>房间列表</title>
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
                                    name: 'room-table',
                                    bordered: true,
                                    pagination: {
                                        pageSize: 10,
                                        pageType: 'client'
                                    },
                                    title: {
                                        text: '游戏房间列表',
                                        basicWidget: [
                                            'setPageSize',
                                            'export',
                                            'switchTags',
                                            'fullScreen'
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
                                            title: '图标',
                                            dataIndex: 'avatar',
                                            render: function (text) {
                                                if (text) {
                                                    return {
                                                        type: 'img',
                                                        src: text,
                                                        width: 60
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            title: '房主',
                                            dataIndex: 'owner'
                                        },
                                        {
                                            title: '管理员',
                                            dataIndex: 'admin',
                                            ellipsis: true,
                                            render: function (text, record) {
                                                return text.join(',');
                                            }
                                        },
                                        {
                                            title: '配置',
                                            dataIndex: 'cfg',
                                            textType: 'json',
                                            ellipsis: true
                                        },
                                        {
                                            title: '规则',
                                            dataIndex: 'rules',
                                            ellipsis: true,
                                            editable: function (text, record) {
                                                return {
                                                    type: 'input',
                                                    name: 'rules',
                                                    rules: {
                                                        required: true
                                                    },
                                                    api: {
                                                        url: '/admin/changeRoomRules',
                                                        method: 'post',
                                                        paramsHandler: function () {
                                                            return {
                                                                id: record.id,
                                                                rules: UF('rules').getValue()
                                                            }
                                                        },
                                                        onSuccess: function (data) {
                                                            UF('room-table').refresh();
                                                        }
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            title: '操作',
                                            data: '_operation',
                                            render: function (text, record) {
                                                return {
                                                    type: 'button',
                                                    mode: 'failure',
                                                    content: '删除',
                                                    onClick: function () {
                                                        UF.Modal.confirm({
                                                            title: '提示',
                                                            content: '确认删除？删除后不可恢复!!!',
                                                            onOk: function () {
                                                                UF.ajax({
                                                                    url: '/admin/delRoom',
                                                                    params: {
                                                                        id: record.id
                                                                    },
                                                                    method: 'delete',
                                                                    success: function () {
                                                                        UF('room-table').refresh();
                                                                    }
                                                                });
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        }
                                    ],
                                    source: '/admin/roomList'
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
