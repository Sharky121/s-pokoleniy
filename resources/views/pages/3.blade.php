@extends('pages_layouts.base')

@inject('activePages', 'activePages')

<?php
    $activeProjectsPages = $activePages->where('parent_page_id', '=', $currentPage->id);
?>

@section('content')

    @include('pages_partials.aside')

    <main class="main">

        @foreach($activeProjectsPages as $projectPage)
            <a href="{{ route("pages.{$projectPage->id}") }}" class="vertical-block">
                <img src="/images/icons/main-{{ $projectPage->class }}.svg" alt="">
                <div>
                    <h2 class="fs16">{{ $projectPage->menu }}</h2>
                </div>
                <div class="vertical-block__image" style="background-image:url('/images/main-{{ $projectPage->class }}.jpg')"></div>
            </a>
        @endforeach

    </main>

@endsection