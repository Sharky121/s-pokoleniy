@extends('pages_layouts.base')

@section('content')

    @foreach ($currentPage->galleries as $gallery)
        <var data-gallery-id="{{ $gallery->key }}" style="display:none !important">
            [
            @foreach ($gallery->photos()->orderBy('position')->get() as $photo)
                {"url":"{{ $photo->path }}","preview":"{{ cherry_thumb($photo->path) }}"}@if (!$loop->last),@endif
            @endforeach
            ]
        </var>
    @endforeach

    @include('pages_partials.aside')

    <main class="blocks">

        <div class="block block__full-width">
            <h1>{!! $sub($currentPage->title) !!}</h1>

            {!! $sub($currentPage->content_0) !!}

            <ul class="two-columns">
                <li style="background-image:url('/images/icons/contacts-placemark.svg')">{!! $sub($currentPage->content_2) !!}</li>
                <li style="background-image:url('/images/icons/contacts-email.svg')">{!! $sub($currentPage->content_3) !!}</li>
            </ul>
            <div id="yaMap"></div>

            {!! $sub($currentPage->content_4) !!}

            @foreach ($currentPage->galleries as $gallery)
                <div class="images">
                    @foreach ($gallery->photos()->orderBy('position')->get() as $i => $photo)
                        <a href="javascript:void(0)" data-gallery="{{ $gallery->key }}" data-index="{{ $i }}" style="background-image:url('{{ cherry_thumb($photo->path) }}')">
                            <div class="overlay">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><circle cx="16" cy="16" r="14"/><path d="M8 16h16z M16 8v16z"/></svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>

        <blockquote></blockquote>

        @include('pages_partials.nav')

    </main>

@endsection

@section('js')
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    @parent
@endsection
