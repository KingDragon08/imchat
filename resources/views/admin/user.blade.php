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
                                type: 'content',
                                style: {background: '#ffffff', color: '#333', minHeight: window.innerHeight - 164},
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
                                                name: 'user',
                                                showSearch: true,
                                                placeholder: '模糊搜索用户名',
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
                                                    
                                                }
                                            },
                                            {
                                                type: 'button',
                                                mode: 'primary',
                                                icon: 'plus',
                                                content: '添加',
                                                onClick: function () {
                                                    alert(1)
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
                                            editable: {
                                                type: 'input',
                                                name: 'password',
                                                rules: {
                                                    required: true,
                                                    min: 6
                                                },
                                                api: '/admin/changeUserPassword'
                                            }
                                        },
                                        {
                                            title: '积分【分】',
                                            dataIndex: 'jifen',
                                            editable: {
                                                type: 'input',
                                                name: 'jifen',
                                                rules: {
                                                    required: true,
                                                    min: 0,
                                                    type: 'number'
                                                },
                                                api: '/admin/changeUserJifen'
                                            }
                                        },
                                        {
                                            title: '红包',
                                            dataIndex: 'bonus',
                                            editable: {
                                                type: 'input',
                                                name: 'bonus',
                                                rules: {
                                                    required: true,
                                                    min: 0,
                                                    type: 'number'
                                                },
                                                api: '/admin/changeUserBonus'
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
                                            title: '手机',
                                            dataIndex: 'phone'
                                        },
                                        {
                                            title: '邮箱',
                                            dataIndex: 'email'
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
