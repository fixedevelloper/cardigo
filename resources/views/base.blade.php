<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../../../">
    <meta charset="utf-8">
    <meta name="author" content="Rodrigue">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content=".">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>{!! config('app.name') !!}</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{!! asset('assets/dashlite.min.css') !!}?ver=3.2.3">
    <link id="skin-default" rel="stylesheet" href="{!! asset('assets/theme.css') !!}?ver=3.2.3">
</head>

<body class="nk-body bg-white npc-default pg-auth">
<div class="nk-app-root">
    <!-- main @s -->
    <div class="nk-main ">
        <!-- wrap @s -->
    @yield('content')
    <!-- content @e -->
    </div>
    <!-- main @e -->
</div>
<!-- app-root @e -->
<!-- JavaScript -->
<script src="{!! asset('assets/bundle.js') !!}?ver=3.2.3"></script>
<script src="{!! asset('assets/scripts.js') !!}?ver=3.2.3"></script>


</html>
