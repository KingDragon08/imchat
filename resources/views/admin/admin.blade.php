<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/uf/theme.min.css" />
        <title>管理员</title>
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
                                    name: 'admin-table',
                                    bordered: true,
                                    pagination: {
                                        pageSize: 10,
                                        pageType: 'client'
                                    },
                                    title: {
                                        text: '管理员列表',
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
                                            title: '用户名',
                                            dataIndex: 'username',
                                            editable: function (text, record) {
                                                return {
                                                    type: 'input',
                                                    name: 'name',
                                                    rules: {
                                                        required: true
                                                    },
                                                    api: {
                                                        url: '/admin/changeAdminName',
                                                        method: 'put',
                                                        paramsHandler: function () {
                                                            return {
                                                                id: record.id,
                                                                name: UF('name').getValue()
                                                            }
                                                        },
                                                        onSuccess: function (data) {
                                                            UF('admin-table').refresh();
                                                        }
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
                                                    style: {
                                                        width: '100px'
                                                    },
                                                    rules: {
                                                        required: true,
                                                        min: 6
                                                    },
                                                    api: {
                                                        url: '/admin/changeAdminPassword',
                                                        method: 'put',
                                                        paramsHandler: function () {
                                                            return {
                                                                id: record.id,
                                                                password: UF('password').getValue()
                                                            }
                                                        },
                                                        onSuccess: function (data) {
                                                            UF('admin-table').refresh();
                                                        }
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            title: '创建时间',
                                            dataIndex: 'created_time',
                                            render: function (text) {
                                                if (text) {
                                                    Date.prototype.Format = function (fmt) {
                                                        var o = {
                                                                "M+": this.getMonth() + 1, // 月份
                                                                "d+": this.getDate(), // 日
                                                                "h+": this.getHours(), // 小时
                                                                "m+": this.getMinutes(), // 分
                                                                "s+": this.getSeconds(), // 秒
                                                                "q+": Math.floor((this.getMonth() + 3) / 3), // 季度
                                                                "S": this.getMilliseconds() // 毫秒
                                                        };
                                                        if (/(y+)/.test(fmt))
                                                            fmt = fmt.replace(RegExp.$1, (this.getFullYear() + ""));
                                                        for (var k in o)
                                                            if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
                                                        return fmt;
                                                    }
                                                    return new Date(text * 1000).Format('yy-MM-dd hh:mm:ss');
                                                }
                                            }  
                                        },
                                        {
                                            title: '角色',
                                            dataIndex: 'role'
                                        }
                                    ],
                                    source: '/admin/admins'
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
