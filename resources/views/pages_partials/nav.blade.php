@inject('activePages', 'activePages')

<?php
$activeProjectsPages = $activePages->where('parent_page_id', '=', 3);
?>

<nav>
    @foreach($activeProjectsPages as $projectPage)
        <a href="{{ route("pages.{$projectPage->id}") }}">
            <img src="/images/icons/main-{{ $projectPage->class }}.svg" alt="">
            <div>{{ $projectPage->menu }}</div>
        </a>
    @endforeach
</nav>