@extends('maket.navigation')
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email.marketing - @yield('title')</title>

    @yield('app_css')
    <link rel="stylesheet" href="css/main.css">
    <script src="js/build.js"></script>
</head>

<body>

@section('nav')

@yield('content')

@yield('add_js')
</body>

</html>