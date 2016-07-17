<!DOCTYPE html>
<html>
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <!-- Site Properties -->
    <title>用户登陆</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/semantic.css') }}">
    <script type="text/javascript" src="{{ asset('/js/semantic.min.js') }}"></script>
    <style type="text/css">
        body {
            background-color: #DADADA;
        }
        body > .grid {
            height: 100%;
        }
        .image {
            margin-top: -100px;
        }
        .column {
            max-width: 450px;
        }
        .ui.form .error.message {
            display: block;
        }
    </style>
</head>
<body>
<div class="ui middle aligned center aligned grid">
    <div class="column">
        <h2 class="ui teal image header">
            <div class="content">
                后台管理系统登陆
            </div>
        </h2>
        <form class="ui large form" method="POST" action="/admin/auth/login">
            {{ csrf_field() }}
            <div class="ui stacked segment">
                <div class="field">
                    <div class="ui left icon input">
                        <i class="user icon"></i>
                        <input type="text" name="name" placeholder="用户名">
                    </div>
                </div>
                <div class="field">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input type="password" name="password" placeholder="密码">
                    </div>
                </div>
                <button class="ui fluid large teal submit button">登陆</button>
            </div>

            @if (session('error'))
            <div class="ui error message">{{ session('error') }}</div>
            @endif
        </form>
    </div>
</div>
</body>


