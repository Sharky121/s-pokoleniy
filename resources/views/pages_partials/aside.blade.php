@inject('activeRootPages', 'activeRootPages')

<aside>
    <a href="/" class="aside__top">
        {!! $sub($layout->content_0) !!}
    </a>

    <div class="aside__middle">
        @foreach ($activeRootPages as $page)
        <a href="{{ route("pages.{$page->id}") }}" class="@if ($currentPage->id == $page->id || data_get($currentPage, 'parent.id') == $page->id || data_get($currentPage, 'parent.parent.id') == $page->id) active @endif">{!! $sub($page->menu) !!}</a>
        @endforeach
    </div>

    <div class="aside__bottom">
        {!! $sub($layout->content_1) !!}
    </div>
</aside>