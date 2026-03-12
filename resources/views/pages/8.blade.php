@extends('pages_layouts.base')

@inject('activePartners', 'activePartners')

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

            @foreach($activePartners as $partner)
                <div class="partner">
                    <img src="{{ $partner->cover }}" alt="{{$partner->title}}">
                    <div>
                        <div class="partner__title">{{ $sub($partner->title) }}</div>
                        <div class="partner__desc">{{ $sub($partner->description) }}</div>
                        <a href="http://{{ $partner->site }}" class="partner__site" target="_blank" rel="noopener noreferrer">{{ $partner->site }}</a>
                    </div>
                </div>
            @endforeach

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
