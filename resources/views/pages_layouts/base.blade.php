<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <meta name="format-detection" content="telephone=no">
    <link href="https://fonts.googleapis.com/css?family=Lora&display=swap&subset=cyrillic" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700,900&display=swap&subset=cyrillic" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">

    @isset($currentPage->title_seo) <title>{{ $sub($currentPage->title_seo) }}</title> @endisset
    @isset($currentPage->keywords_seo) <meta name="keywords" content="{{ $sub($currentPage->keywords_seo) }}" /> @endisset
    @isset($currentPage->description_seo) <meta name="description" content="{{ $sub($currentPage->description_seo) }}" /> @endisset

    @section('css')
    @endsection
</head>

@inject('activeQuotes', 'activeQuotes')

<body class="no-projects">
    <div class="wrapper">
        <div class="content">

        @include('pages_partials.header')

        <div class="main-content">
            @yield('content')
            <div class="menu-backdrop" data-menu-action="close"></div>
        </div>

        </div>
    </div>

    @section('footer')
    @show

    @section('js')
        <script type="text/javascript">
            window.QUOTES = [
                @foreach ($activeQuotes as $quote)
                {quote: '{{ $quote->content }}', author: '{{ $quote->author }}'} @if (!$loop->last),@endif
                @endforeach
            ];
        </script>

        <script type="text/javascript" src="/js/app.js"></script>
        <script type="text/javascript" src="/js/gallery.js"></script>
        <script type="text/javascript">Gallery.initialize();</script>
    @show
</body>
</html>
