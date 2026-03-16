@extends('pages_layouts.base')

@switch ($currentPage->id)
    @case (10)
        @inject('collection', 'activeUnionArticles')
        <?php $pageId = null; ?>
        <?php $item = $news; ?>
        @break
    @case (16)
        @inject('collection', 'activeChurches')
        <?php $pageId = 16; ?>
        <?php $item = $churches; ?>
        @break
    @case (17)
        @inject('collection', 'activePublishings')
        <?php $pageId = 17; ?>
        <?php $item = $publishings; ?>
        @break
    @case (18)
        @inject('collection', 'activeOrphans')
        <?php $pageId = 18; ?>
        <?php $item = $orphans; ?>
        @break
    @case (19)
        @inject('collection', 'activeVeterans')
        <?php $pageId = 19; ?>
        <?php $item = $veterans; ?>
        @break
    @case (20)
        @inject('collection', 'activeArt')
        <?php $pageId = 20; ?>
        <?php $item = $art; ?>
        @break
@endswitch

<?php
    // Пагинация
    $current = $collection->search(function ($candidate, $key) use ($item) {
        return get_class($candidate) == get_class($item) && $candidate->id == $item->id;
    });
    $previous = $collection->get($current + 1);
    $next = $collection->get($current - 1);
?>

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
            <h1>{!! $sub($item->title) !!}</h1>
            <small>{!! $item->date->monthName . ' ' . $item->date->year !!}</small>
            <hr class="gray">
            <div class="yellow-link">
                <a href="{{ route("pages.{$currentPage->parent->id}") }}">{!! $sub($currentPage->parent->title) !!}</a>
            </div>
        </div>

        @if($item->photos->count() > 1)
            <div class="block block__full-width" data-simple-slider>
                @foreach($item->photos->sortBy('position') as $photo)
                    <div style="background-image:url('{{ '/' . ltrim($photo->path ?? '', '/') }}')" data-ss-image @if ($loop->first) class="current" @endif></div>
                @endforeach
                <div class="white-line"></div>
                <div class="image-text">
                    <span>{!! $sub($item->title) !!}</span>
                    <span class="yellow">@if ($item->place) {!! $sub($item->place) !!} @endif {!! $item->date->monthName . ' ' . $item->date->year !!}</span>
                </div>
                <div data-ss-action="prev"><img src="/images/icons/arrow.svg" alt="<<"></div>
                <div data-ss-action="next"><img src="/images/icons/arrow.svg" alt=">>"></div>
            </div>
        @else
            <div class="block block__full-width block__no-padding">
                <?php $photoPath = data_get($item, 'photos.0.path', $item->cover); ?>
                <img src="{{ '/' . ltrim($photoPath ?? '', '/') }}" alt="">
            </div>
        @endif

        <div class="block block__full-width">
            <div class="content-long">{!! $sub($item->content_long) !!}</div>
        </div>

        @if ($previous)
            <?php $currentPageId = $pageId ?? $previous->page_id; ?>

            <a href="{{ route("pages.{$currentPageId}", [$previous->id, str_slug($sub($previous->title))]) }}" class="block block__half">
                <img src="{{ '/' . ltrim($previous->cover ?? '', '/') }}" alt="">
                <div>
                    <div class="case-nav__title">Предыдущий проект</div>
                    <div class="case-nav__desc">{!! $sub($previous->title) !!}</div>
                </div>
            </a>
        @else
            <a href="javascript:void(0)" class="block block__half" style="visibility: hidden;">
                <div>
                    <div class="case-nav__title">Предыдущий проект</div>
                    <div class="case-nav__desc">---</div>
                </div>
            </a>
        @endif

        @if ($next)
            <?php $currentPageId = $pageId ?? $next->page_id; ?>

            <a href="{{ route("pages.{$currentPageId}", [$next->id, str_slug($sub($next->title))]) }}" class="block block__half">
                <img src="{{ '/' . ltrim($next->cover ?? '', '/') }}" alt="">
                <div>
                    <div class="case-nav__title">Следующий проект</div>
                    <div class="case-nav__desc">{!! $sub($next->title) !!}</div>
                </div>
            </a>
        @else
            <a href="javascript:void(0)" class="block block__half" style="visibility: hidden;">
                <div>
                    <div class="case-nav__title">Следующий проект</div>
                    <div class="case-nav__desc">---</div>
                </div>
            </a>
        @endif

        <blockquote></blockquote>

        @include('pages_partials.nav')

    </main>

@endsection