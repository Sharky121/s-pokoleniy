@extends('pages_layouts.base')

@switch ($currentPage->id)
    @case (7)
        @inject('collection', 'activeUnionArticles')
        <?php $pageId = null; ?>
        @break
    @case (11)
        @inject('collection', 'activeChurches')
        <?php $pageId = 16; ?>
        @break
    @case (12)
        @inject('collection', 'activePublishings')
        <?php $pageId = 17; ?>
        @break
    @case (13)
        @inject('collection', 'activeOrphans')
        <?php $pageId = 18; ?>
        @break
    @case (14)
        @inject('collection', 'activeVeterans')
        <?php $pageId = 19; ?>
        @break
    @case (15)
        @inject('collection', 'activeArt')
        <?php $pageId = 20; ?>
        @break
@endswitch

@section('content')

    @foreach ($currentPage->galleries as $gallery)
        <var data-gallery-id="{{ $gallery->key }}" style="display:none !important">
            [
            @foreach ($gallery->photos()->orderBy('position')->get() as $photo)
                {"url":"{{ '/' . ltrim($photo->path ?? '', '/') }}","preview":"{{ '/' . ltrim($photo->path ?? '', '/') }}"}@if (!$loop->last),@endif
            @endforeach
            ]
        </var>
    @endforeach

    @include('pages_partials.aside')

    <main class="blocks">

        <div class="block block__full-width">
            <h1>{!! $sub($currentPage->title) !!}</h1>

            {!! $sub($currentPage->content_0) !!}

            @foreach($collection as $item)
                <?php // Хак для списка новостей - убираем дубли ?>
                <?php if (is_null($pageId) && $item instanceof App\Models\Art && $item->id === 6): ?>
                    <?php continue; ?>
                <?php endif; ?>

                <?php $currentPageId = $pageId ?? $item->page_id; ?>

                <a href="{{ route("pages.{$currentPageId}", [$item->id, str_slug($sub($item->title))]) }}" class="news">
                    <img src="{{ '/' . ltrim($item->cover ?? '', '/') }}" alt="">
                    <div>
                        <div class="news__title">{!! $sub($item->title) !!}</div>
                        <time class="news__date">{!! $item->date->monthName . ' ' . $item->date->year !!}</time>
                        <div class="news__preview">{!! $sub($item->preview) !!}</div>
                    </div>
                </a>
            @endforeach

            @foreach ($currentPage->galleries as $gallery)
                <div class="images">
                    @foreach ($gallery->photos()->orderBy('position')->get() as $i => $photo)
                        <a href="javascript:void(0)" data-gallery="{{ $gallery->key }}" data-index="{{ $i }}" style="background-image:url('{{ '/' . ltrim($photo->path ?? '', '/') }}')">
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