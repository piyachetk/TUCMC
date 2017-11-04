<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/fonts/thsarabunnew.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - คณะกรรมการบริหารงานกิจกรรมพัฒนาผู้เรียน โรงเรียนเตรียมอุดมศึกษา</title>

    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
        }

        strong {
            color: red;
            font-size: small;
        }

        .externalLinkImg {
            width: 32px;
            height: 32px;
            margin-right: 5px;
            border-radius: 100%;
        }
    </style>
</head>
<body>
<nav class="pink lighten-2" role="navigation">
    <div class="nav-wrapper container">
        @if(!Request::is('/'))<a id="logo-container" href="/" class="brand-logo white-text" style="font-size: 2rem;">TUCMC</a>@endif
        <ul class="right hide-on-med-and-down">
            <li{{ Request::is('/') ? " class=active" : '' }}><a href="/">หน้าแรก</a></li>
            <li{{ Request::is('/contact') ? " class=active" : '' }}><a href="/contact/">ติดต่อสอบถาม</a></li>
        </ul>

        <ul id="nav-mobile" class="side-nav">
            <li class="active"><a class="center" href="/">หน้าแรก</a></li>
            <li><a class="center" href="/contact/">ติดต่อสอบถาม</a></li>
        </ul>
        <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
    </div>
</nav>

@yield('pre-content')

<main class="container">
    @yield('content')
</main>

<footer class="page-footer pink lighten-2">
    <div class="container white-text" style="padding-bottom:1rem;">
        งานกิจกรรมพัฒนาผู้เรียน โรงเรียนเตรียมอุดมศึกษา<br />
        227 ถนนพญาไท แขวงปทุมวัน เขตปทุมวัน กรุงเทพมหานคร 10330<br />
        โทรศัพท์: 02-254-0287 ต่อ 157 | โทรสาร: 02-252-7002<br />

        <br />

        <a href="https://clubs.triamudom.ac.th">
            <img class="externalLinkImg" src="/img/tucmc.png"/>
        </a>
        <a href="mailto:tucmc@buffalolarity.com">
            <img class="externalLinkImg" src="/img/mail.png"/>
        </a>
        <a href="mailto:triam.club@gmail.com">
            <img class="externalLinkImg" src="/img/mail.png"/>
        </a>
        <a href="https://www.facebook.com/triamudomclubs">
            <img class="externalLinkImg" src="/img/facebook.png"/>
        </a>
        <br/>
    </div>
    <div class="footer-copyright">
        <div class="container">
            <span class="hide-on-screen">Copyright © 2017 Buffalolarity</span>
        </div>
    </div>
</footer>

<script src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/js/materialize.min.js"></script>
<script>
    $(document).ready(function(){
        $(".button-collapse").sideNav();
        @yield('startup-js')
    });
</script>
</body>
</html>