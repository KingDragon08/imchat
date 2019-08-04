<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>登录</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- materialize icon -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Bootstrap Core CSS -->
    <link href="/css/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="/css/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/css/sb-admin-2.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
     <!-- Css Style -->
    <link href="/css/style.css" rel="stylesheet">
</head>
<body class="login-bgmain">
    <div class="container">
        <div class="row">
            
            <div class="login-table">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <img src="/img/logo.png" width="50" />
                        <h3 class="panel-title">imchat</h3>
                    </div>
                    <div class="panel-body">
                        <form action="javascript:;" onsubmit="login()">
                            <fieldset>
                                <div class="form-group">
                                    <i class="fa fa-user login-icon"></i>
                                    <input type="text" id="username" class="form-control" placeholder="Username" name="username" required="" autofocus>
                                </div>
                                <div class="form-group">
                                    <i class="fa fa-unlock-alt login-icon"></i>
                                    <input type="password" id="password" class="form-control" placeholder="Password" name="password" required="">
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-block" value="登  录"></div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="/js/jquery-1.9.1.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="/css/bootstrap/js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="/css/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script>
        function login(e) {
            var username = $('#username').val();
            var password = $('#password').val();
            if (username.length < 4 || password.length < 6) {
                alert('账户名或密码错误,请重试!');
                return;
            }
            $.ajax({
                url: '/admin/login',
                method: 'post',
                data: {
                    username: username,
                    password: password
                },
                success: function (data) {
                    console.log(data);
                    if (data.status == 1) {
                        alert(data.msg);
                        return;
                    }
                    location.href = '/admin/'
                },
                error: function (e) {
                    alert('请求出错');
                }
            });
        }
    </script>

</body>

</html>
