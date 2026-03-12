<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Cherryline\CherrySite;

class PagesController extends Controller
{
    public function index(Request $request)
    {
        $model = new Page();
        $table = $model->getTable();
        $query = $model->newQuery()->orderByDesc('position');

        $widget = new CherrySite\Views\Common\Page(
            'Страницы',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Страницы', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.pages.index'),
                    new CherrySite\Views\Common\CreateButton('admin.pages.create'),
                ]),
                new CherrySite\Views\Widgets\Table\Layout([
                    new CherrySite\Views\Widgets\Table\Text($table, 'id', 'ID'),
                    new CherrySite\Views\Widgets\Table\Text($table, 'title', 'Заголовок'),
                    new CherrySite\Views\Widgets\Table\Text($table, 'menu', 'Меню'),
                    new CherrySite\Views\Widgets\Table\Select($table, 'parent_page_id', 'Родительская страница', app('activePages')->pluck('title', 'id')->toArray()),
                    new CherrySite\Views\Widgets\Table\Text($table, 'url', 'Ссылка'),
                    new CherrySite\Views\Widgets\Table\Numeric($table, 'position', 'Сортировка'),
                    new CherrySite\Views\Widgets\Table\Switcher($table, 'is_active', 'Активен', ['Нет', 'Да']),
                    new CherrySite\Views\Widgets\Table\Actions('Действия', [
                        new CherrySite\Views\Widgets\Table\EditButton('admin.pages.edit'),
                        new CherrySite\Views\Widgets\Table\DeleteButton('admin.pages.destroy'),
                    ]),
                ]),
            ])
        );

        return $widget
            ->setRequest($request)
            ->setBuilder($query)
            ->render();
    }

    public function create(Request $request)
    {
        $model = new Page();

        $table = $model->getTable();
        $query = $model->newQuery();

        $widget = new CherrySite\Views\Common\Page(
            'Новая страница',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Новая страница', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.pages.create'),
                ]),
                new CherrySite\Views\Widgets\Form\TabsContainer([
                    new CherrySite\Views\Widgets\Form\TabItem('Основные настройки', [
                        new CherrySite\Views\Widgets\Form\Layout('admin.pages.store', 'POST', [
                            new CherrySite\Views\Widgets\Form\Text($table, 'title', 'Заголовок'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'class', 'Класс'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'menu', 'Меню'),
                            new CherrySite\Views\Widgets\Form\Select($table, 'parent_page_id', 'Родительская страница', app('activePages')->pluck('title', 'id')->toArray()),
                            new CherrySite\Views\Widgets\Form\Numeric($table, 'position', 'Сортировка'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'url', 'Ссылка'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'title_seo', 'Заголовок (СЕО)'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'keywords_seo', 'Ключевые слова (СЕО)'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'description_seo', 'Описание (СЕО)'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_0', 'Содержимое, часть 1'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_1', 'Содержимое, часть 2'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_2', 'Содержимое, часть 3'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_3', 'Содержимое, часть 4'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_4', 'Содержимое, часть 5'),
                            new CherrySite\Views\Widgets\Form\Switcher($table, 'is_active', 'Активен', ['Нет', 'Да']),
                        ]),
                    ]),
                    // В дизайне не предусмотрены галереи страниц
                    // new CherrySite\Views\Widgets\Form\TabItemDisabled('Галереи'),
                ]),
            ])
        );

        return $widget
            ->setRequest($request)
            ->setBuilder($query)
            ->render();
    }

    public function edit(Request $request, Page $model)
    {
        $table = $model->getTable();

        $widget = new CherrySite\Views\Common\Page(
            'Редактирование страницы',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Редактирование страницы', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.pages.edit', $model),
                    new CherrySite\Views\Common\DeleteButton('admin.pages.destroy'),
                ]),
                new CherrySite\Views\Widgets\Form\TabsContainer([
                    new CherrySite\Views\Widgets\Form\TabItem('Основные настройки', [
                        new CherrySite\Views\Widgets\Form\Layout('admin.pages.update', 'PATCH', [
                            new CherrySite\Views\Widgets\Form\Text($table, 'title', 'Заголовок'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'class', 'Класс'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'menu', 'Меню'),
                            new CherrySite\Views\Widgets\Form\Select($table, 'parent_page_id', 'Родительская страница', app('activePages')->pluck('title', 'id')->toArray()),
                            new CherrySite\Views\Widgets\Form\Numeric($table, 'position', 'Сортировка'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'url', 'Ссылка'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'title_seo', 'Заголовок (СЕО)'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'keywords_seo', 'Ключевые слова (СЕО)'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'description_seo', 'Описание (СЕО)'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_0', 'Содержимое, часть 1'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_1', 'Содержимое, часть 2'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_2', 'Содержимое, часть 3'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_3', 'Содержимое, часть 4'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_4', 'Содержимое, часть 5'),
                            new CherrySite\Views\Widgets\Form\Switcher($table, 'is_active', 'Активен', ['Нет', 'Да']),
                        ]),
                    ]),
                    // В дизайне не предусмотрены галереи страниц
                    // new CherrySite\Views\Widgets\Form\TabItemLink('Галереи', route('admin.pages.pages_galleries.index', [$model], false)),
                ]),
            ])
        );

        return $widget
            ->setRequest($request)
            ->setModel($model)
            ->render();
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'required|string',
            'class' => 'nullable|string',
            'menu' => 'nullable|string',
            'parent_page_id' => 'nullable|integer',
            'position' => 'nullable|integer',
            'url' => 'nullable|string',
            'title_seo' => 'nullable|string',
            'keywords_seo' => 'nullable|string',
            'description_seo' => 'nullable|string',
            'content_0' => 'nullable|string',
            'content_1' => 'nullable|string',
            'content_2' => 'nullable|string',
            'content_3' => 'nullable|string',
            'content_4' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ], [], [
            'title' => 'Заголовок',
            'class' => 'Класс',
            'menu' => 'Меню',
            'parent_page_id' => 'Родительская страница',
            'position' => 'Сортировка',
            'url' => 'Ссылка',
            'title_seo' => 'Заголовок (СЕО, пустой для автогенерации)',
            'keywords_seo' => 'Ключевые слова (СЕО, пустой для автогенерации)',
            'description_seo' => 'Описание (СЕО, пустой для автогенерации)',
            'content_0' => 'Содержимое, часть 1',
            'content_1' => 'Содержимое, часть 2',
            'content_2' => 'Содержимое, часть 3',
            'content_3' => 'Содержимое, часть 4',
            'content_4' => 'Содержимое, часть 5',
            'is_active' => 'Активен',
        ]);

        $connection = Page::resolveConnection();
        $query = Page::query();

        $modifier = new CherrySite\Modifiers\Model\Transaction($connection, [
            new CherrySite\Modifiers\Single\Creator($data, $query, [
                new CherrySite\Modifiers\Model\Text('title'),
                new CherrySite\Modifiers\Model\Text('class'),
                new CherrySite\Modifiers\Model\Text('menu'),
                new CherrySite\Modifiers\Model\Text('parent_page_id'),
                new CherrySite\Modifiers\Model\Numeric('position'),
                new CherrySite\Modifiers\Model\Text('url'),
                new CherrySite\Modifiers\Model\Text('title_seo'),
                new CherrySite\Modifiers\Model\Text('keywords_seo'),
                new CherrySite\Modifiers\Model\Text('description_seo'),
                new CherrySite\Modifiers\Model\Text('content_0'),
                new CherrySite\Modifiers\Model\Text('content_1'),
                new CherrySite\Modifiers\Model\Text('content_2'),
                new CherrySite\Modifiers\Model\Text('content_3'),
                new CherrySite\Modifiers\Model\Text('content_4'),
                new CherrySite\Modifiers\Model\Switcher('is_active'),
                new CherrySite\Modifiers\Model\Value('view', 'pages.text'),
            ], new CherrySite\Modifiers\Helpers\Pipe([
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('url', ['id', ['str_slug', 'title']]),
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('title_seo', [['strip_tags', 'title']]),
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('description_seo', ['title']),
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('keywords_seo', ['title']),
                $retriever = new CherrySite\Modifiers\Helpers\Retriever(),
            ])),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.pages.edit', [$retriever->getModel()], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function update(Request $request, Page $model)
    {
        $data = $this->validate($request, [
            'title' => 'required|string',
            'class' => 'nullable|string',
            'menu' => 'nullable|string',
            'parent_page_id' => 'nullable|integer',
            'position' => 'nullable|integer',
            'url' => 'nullable|string',
            'title_seo' => 'nullable|string',
            'keywords_seo' => 'nullable|string',
            'description_seo' => 'nullable|string',
            'content_0' => 'nullable|string',
            'content_1' => 'nullable|string',
            'content_2' => 'nullable|string',
            'content_3' => 'nullable|string',
            'content_4' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ], [], [
            'title' => 'Заголовок',
            'class' => 'Класс',
            'menu' => 'Меню',
            'parent_page_id' => 'Родительская страница',
            'position' => 'Сортировка',
            'url' => 'Ссылка',
            'title_seo' => 'Заголовок (СЕО, пустой для автогенерации)',
            'keywords_seo' => 'Ключевые слова (СЕО, пустой для автогенерации)',
            'description_seo' => 'Описание (СЕО, пустой для автогенерации)',
            'content_0' => 'Содержимое, часть 1',
            'content_1' => 'Содержимое, часть 2',
            'content_2' => 'Содержимое, часть 3',
            'content_3' => 'Содержимое, часть 4',
            'content_4' => 'Содержимое, часть 5',
            'is_active' => 'Активен',
        ]);

        $connection = $model->getConnection();

        $modifier = new CherrySite\Modifiers\Model\Transaction($connection, [
            new CherrySite\Modifiers\Single\Updater($data, $model, [
                new CherrySite\Modifiers\Model\Text('title'),
                new CherrySite\Modifiers\Model\Text('class'),
                new CherrySite\Modifiers\Model\Text('menu'),
                new CherrySite\Modifiers\Model\Text('parent_page_id'),
                new CherrySite\Modifiers\Model\Numeric('position'),
                new CherrySite\Modifiers\Model\Text('url'),
                new CherrySite\Modifiers\Model\Text('title_seo'),
                new CherrySite\Modifiers\Model\Text('keywords_seo'),
                new CherrySite\Modifiers\Model\Text('description_seo'),
                new CherrySite\Modifiers\Model\Text('content_0'),
                new CherrySite\Modifiers\Model\Text('content_1'),
                new CherrySite\Modifiers\Model\Text('content_2'),
                new CherrySite\Modifiers\Model\Text('content_3'),
                new CherrySite\Modifiers\Model\Text('content_4'),
                new CherrySite\Modifiers\Model\Switcher('is_active'),
            ], new CherrySite\Modifiers\Helpers\Pipe([
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('url', ['id', ['str_slug', 'title']]),
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('title_seo', [['strip_tags', 'title']]),
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('description_seo', ['title']),
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('keywords_seo', ['title']),
                $retriever = new CherrySite\Modifiers\Helpers\Retriever(),
            ])),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.pages.index', [], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function destroy(Request $request, Page $model)
    {
        $modifier = new CherrySite\Modifiers\Model\Delete($model);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.pages.index', [], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }
}