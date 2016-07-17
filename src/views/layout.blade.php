<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<title>
    @hasSection('title')
    @yield('title')
    @else
    {{ Config::get('admin.name') }}
    @endif
</title>

<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/ionicons.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/AdminLTE.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/skin-blue.min.css') }}">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-blue">
<div class="wrapper">
    @include('header')

    @include('sidebar')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                @hasSection('title')
                @yield('title')
                @else
                {{ Config::get('admin.name') }}
                @endif
            </h1>
            <small>
                xxx xxx
            </small>
        </section>
        <section class="content">
            @yield('content')
        </section>
    </div>

    @include('footer')


    <div class="ui inverted menu top-menu" style="margin-bottom:0;border-radius:0;">
        <a href="#" class="header item">
            <img style="margin-right:1.5em" src="{{ asset('img/logo.png') }}">
            {{ Config::get('admin.name') }}
        </a>
        <div class="right menu" style="border-radius:0;">
            <div class="ui dropdown item" tabindex="0">
                <i class="user icon"></i>admin                    
                <div class="menu transition hidden" tabindex="-1">
                    <a class="item" href="/admin/auth/logout"><i class="sign out icon"></i>注销</a>
                </div>
            </div>
        </div>
    </div>
    <div style="display:flex;flex:1;">
        <div class="left sidebar" style="background-color:#1b1c1d;">
            <div class="ui inverted vertical visible menu" style="border-radius:0;">
                @foreach (Friparia\Admin\Models\Menu::where('pid', 0)->get() as $menu)
                <div class="item">
                    <div class="header">{{ $menu->name }}</div>
                    <div class="menu">
                        @foreach(Friparia\Admin\Models\Menu::where('pid', $menu->id)->get() as $submenu)
                        @if(Auth::user()->canVisit($submenu->url))
                        <a class="item" href="/admin/{{ $submenu->url }}">
                            {{ $submenu->name }}
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="container" style="flex:1;overflow-y:auto;padding-top:40px;padding-left:30px;padding-right:30px;">
            @if (session('error'))
            <div class="ui negative message">
                <p>{{ session('error') }}</p>
            </div>
            @endif
            @if (session('success'))
            <div class="ui success message">
                <p>{{ session('success') }}</p>
            </div>
            @endif
            <h2 class="ui dividing header">
                @hasSection('title')
                @yield('title')
                @else
                {{ Config::get('admin.name') }}
                @endif
            </h2>
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>
    <div class="footer"></div>
</div>
<script type="text/javascript" src="{{ asset('/js/jquery-1.10.2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/app.min.js') }}"></script>
</body>
</html>


