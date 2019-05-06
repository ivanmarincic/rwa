<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#c73840">
    <meta name="msapplication-navbutton-color" content="#c73840">
    <meta name="apple-mobile-web-app-status-bar-style" content="#c73840">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="/mi-chat">

    <title>MI Chat</title>

    <link href="/mi-chat/resources/material-icons/material-icons.css" rel="stylesheet">
    <link href="/mi-chat/resources/bootstrap/bootstrap-reboot.min.css" rel="stylesheet" type="text/css">
    <link href="/mi-chat/resources/bootstrap/bootstrap-grid.min.css" rel="stylesheet" type="text/css">
    <link href="/mi-chat/resources/sass/app.css" rel="stylesheet" type="text/css">
    <link href="/mi-chat/resources/sass/dropdown.css" rel="stylesheet" type="text/css">
    @yield('included-css')
</head>
<body>
<header>
    <div class="container">
        <div class="row">
            <div class="col-6 d-flex flex-row justify-content-start align-items-center">
                <a href="{{route("home")}}">
                    <h2 class="title">
                        MI Chat
                    </h2>
                </a>
            </div>
            @auth
                @php
                    $user = Auth::user();
                    $name = "";
                    if(is_null($user->full_name) || empty($user->full_name)){
                        $name =  $user->username;
                    } else {
                        $name =  $user->full_name;
                    }
                @endphp
                <div class="col-6 d-flex flex-row justify-content-end align-items-center">
                    <div class="menu">
                        <div class="selected-item">
                            {{$name}}
                        </div>
                        <div class="dropdown">
                            <div class="item" data-action="profile">
                                Moj profil
                            </div>
                            <div class="item" data-action="logout">
                                Odjavi se
                            </div>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>
<div class="content">
    @yield('content')
</div>
<div class="dialog-container" style="display: none;">
    @yield('dialogs')
</div>
</body>
<script src="/mi-chat/resources/js/lib/formdata-polyfill.min.js"></script>
<script src="/mi-chat/resources/js/lib/weakmap-polyfill.min.js"></script>
<script src="/mi-chat/resources/js/lib/jquery-3.3.1.min.js"></script>
<script src="/mi-chat/resources/js/lib/jquery.ba-throttle-debounce.min.js"></script>
<script src="/mi-chat/resources/js/lib/pusher.min.js"></script>
<script src="/mi-chat/resources/js/lib/echo.iife.js"></script>
<script>
    var csrfToken = "{{ csrf_token() }}";
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: 'cecb830d34ef8979a632',
        authEndpoint: '/mi-chat/web/broadcasting/auth',
        cluster: 'eu',
        encrypted: true,
        disableStats: true
    });
</script>
<script src="/mi-chat/resources/js/app/api.js"></script>
<script src="/mi-chat/resources/js/app/globals.js"></script>
<script src="/mi-chat/resources/js/app/checkbox.js"></script>
<script src="/mi-chat/resources/js/app/dialog.js"></script>
<script src="/mi-chat/resources/js/app/dropdown.js"></script>
<script src="/mi-chat/resources/js/app/progress.js"></script>
<script src="/mi-chat/resources/js/app/app.js"></script>
<script src="/mi-chat/resources/js/app/hide-address-bar.js"></script>
@yield('included-js')
</html>
