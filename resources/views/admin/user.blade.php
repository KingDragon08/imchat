<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/uf/theme.min.css" />
        <title>用户列表</title>
    </head>
    <body>
        <div id="uf"></div>

    </body>
    <script src="/js/base64.js"></script>
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
                                    name: 'user-table',
                                    bordered: true,
                                    pagination: {
                                        pageSize: 10,
                                        pageType: 'server'
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
                                                type: 'input',
                                                name: 'agent',
                                                showSearch: true,
                                                placeholder: '按代理搜索',
                                                style: {
                                                    width: '200px',
                                                    marginLeft: '14px'
                                                },
                                                allowClear: true
                                            },
                                            {
                                                type: 'input',
                                                name: 'user',
                                                showSearch: true,
                                                placeholder: '按用户名搜索',
                                                style: {
                                                    width: '200px',
                                                    marginLeft: '14px'
                                                },
                                                allowClear: true
                                            },
                                            {
                                                type: 'button',
                                                content: '搜索',
                                                icon: 'search',
                                                style: {
                                                    margin: '14px',
                                                },
                                                onClick: function () {
                                                    var agent = UF('agent').getValue();
                                                    var user = UF('user').getValue();
                                                    var params = {};
                                                    if (agent && agent.length) {
                                                        params.agent = agent;
                                                    }
                                                    if (user && user.length) {
                                                        params.user = user;
                                                    }
                                                    if (params.hasOwnProperty('agent') ||
                                                        params.hasOwnProperty('user')) {
                                                        UF('user-table').set({
                                                            params: params
                                                        });
                                                    }
                                                }
                                            },
                                            {
                                                type: 'button',
                                                mode: 'primary',
                                                content: '重置',
                                                onClick: function () {
                                                    UF('user-table').set({
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
                                            title: '用户名',
                                            dataIndex: 'username'
                                        },
                                        {
                                            title: '昵称',
                                            dataIndex: 'nickname'
                                        },
                                        {
                                            title: '代理',
                                            dataIndex: 'agent'
                                        },
                                        {
                                            title: '头像',
                                            dataIndex: 'avatar',
                                            render: function (text, record) {
                                                return {
                                                    type: 'img',
                                                    src: text,
                                                    style: {
                                                        width: '50px'
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            title: '密码',
                                            dataIndex: 'password',
                                            width: 100,
                                            ellipsis: true,
                                            editable: function (text, record) {
                                                return {
                                                    type: 'input',
                                                    name: 'password',
                                                    rules: {
                                                        required: true,
                                                        min: 6
                                                    },
                                                    api: {
                                                        url: '/admin/changeUserPassword',
                                                        paramsHandler: function () {
                                                            return {
                                                                id: record.id,
                                                                password: record.password,
                                                                npassword: UF('password').getValue()
                                                            }
                                                        },
                                                        onSuccess: function (data) {
                                                            UF('user-table').refresh();
                                                        }
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            title: '积分【分】',
                                            dataIndex: 'jifen',
                                            editable: function (text, record) {
                                                return {
                                                    type: 'input',
                                                    name: 'jifen',
                                                    rules: {
                                                        required: true,
                                                        min: 0,
                                                        type: 'number'
                                                    },
                                                    api: {
                                                        url: '/admin/changeUserJifen',
                                                        paramsHandler: function () {
                                                            return {
                                                                id: record.id,
                                                                jifen: UF('jifen').getValue()
                                                            }
                                                        },
                                                        onSuccess: function (data) {
                                                            UF('user-table').refresh();
                                                        }
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            title: '红包【分】',
                                            dataIndex: 'bonus',
                                            editable: function (text, record) {
                                                return {
                                                    type: 'input',
                                                    name: 'bonus',
                                                    rules: {
                                                        required: true,
                                                        min: 0,
                                                        type: 'number'
                                                    },
                                                    api: {
                                                        url: '/admin/changeUserBonus',
                                                        paramsHandler: function () {
                                                            return {
                                                                id: record.id,
                                                                bonus: UF('bonus').getValue()
                                                            };
                                                        },
                                                        onSuccess: function (data) {
                                                            UF('user-table').refresh();
                                                        }
                                                    }
                                                };
                                            }
                                        },
                                        {
                                            title: '创建时间',
                                            dataIndex: 'created_at',
                                            render: function (text, record) {
                                                return util.transTimestamp(text * 1000);
                                            }
                                        },
                                        {
                                            title: '签名',
                                            dataIndex: 'sign'
                                        },
                                        {
                                            title: '分享码',
                                            dataIndex: '_',
                                            render: function (text, record) {
                                                if (record) {
                                                    return Base64.encode(record.id);
                                                }
                                            }
                                        },
                                        {
                                            title: '手机',
                                            dataIndex: 'phone'
                                        },
                                        {
                                            title: '邮箱',
                                            dataIndex: 'email'
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
                                                                    url: '/admin/delUser',
                                                                    params: {
                                                                        id: record.id
                                                                    },
                                                                    method: 'post',
                                                                    success: function () {
                                                                        UF('user-table').refresh();
                                                                    }
                                                                });
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        }
                                    ],
                                    source: '/admin/userList'
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
