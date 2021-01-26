<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js"></script>
    
    <!-- JQuery UI -->
    <script src ="{{ asset('js/jquery-ui-1.12.1.custom/jquery-ui.js')  }}"></script> 
    <link rel="stylesheet" href="{{ asset('js/jquery-ui-1.12.1.custom/jquery-ui.css' ) }}">
    <link rel="stylesheet" href="{{ asset('js/jquery-ui-1.12.1.custom/jquery-ui.theme.css' ) }}">
    <link rel="stylesheet" href="{{ asset('js/jquery-ui-1.12.1.custom/ui/i18n/datepicker-ja.js' ) }}">

    <!-- Bootstrap -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

    <!-- TimePiker JS -->
    <script src="{{ asset( 'js/jquery.timepicker.js' ) }}"></script>
    <script src="{{ asset( 'js/jquery.timepicker.min.js' ) }}"></script>
    <link rel="stylesheet" href="{{ asset( 'css/jquery.timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset( 'css/jquery.timepicker.min.css') }}">
    
    <!-- 郵便番号検索 -->
    <script src="{{ asset( 'js/ajaxzip3.js' ) }}" chareset="UTF-8"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!--Icons-->
    <script src="https://kit.fontawesome.com/bb339603e2.js" crossorigin="anonymous"></script>
    <!--<script src="https://unpkg.com/ionicons@5.1.2/dist/ionicons.js"></script>-->
    
    <script type="module" src="https://unpkg.com/ionicons@5.1.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule="" src="https://unpkg.com/ionicons@5.1.2/dist/ionicons/ionicons.js"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}"     rel="stylesheet">
    <link href="{{ asset('css/myStyle.css') }}" rel="stylesheet">

</head>
