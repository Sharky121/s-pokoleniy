<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orphan;
use Illuminate\Http\Request;
use Cherryline\CherrySite;

class OrphansController extends Controller
{
    public function index(Request $request)
    {
        $model = new Orphan();
        $table = $model->getTable();
        $query = $model->newQuery()->orderBy('date', 'desc');
        $paginator = $query->simplePaginate(20);

        $widget = new CherrySite\Views\Common\Page(
            'Помощь детям',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Помощь детям', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.orphans.index'),
                    new CherrySite\Views\Common\CreateButton('admin.orphans.create'),
                ]),
                new CherrySite\Views\Widgets\Table\Layout([
                    new CherrySite\Views\Widgets\Table\Text($table, 'id', 'ID'),
                    new CherrySite\Views\Widgets\Table\Text($table, 'title', 'Название'),
                    new CherrySite\Views\Widgets\Table\DateTime($table, 'date', 'Дата', 'd.m.Y'),
                    new CherrySite\Views\Widgets\Table\Switcher($table, 'is_active', 'Активен', ['Нет', 'Да']),
                    new CherrySite\Views\Widgets\Table\Actions('Действия', [
                        new CherrySite\Views\Widgets\Table\EditButton('admin.orphans.edit'),
                        new CherrySite\Views\Widgets\Table\DeleteButton('admin.orphans.destroy'),
                    ]),
                ], $paginator),
            ])
        );

        return $widget
            ->setRequest($request)
            ->setBuilder($query)
            ->render();
    }

    public function create(Request $request)
    {
        $model = new Orphan();
        $table = $model->getTable();
        $query = $model->newQuery();

        $widget = new CherrySite\Views\Common\Page(
            'Новая новость помощи детям',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Новая новость помощи детям', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.orphans.create'),
                ]),
                new CherrySite\Views\Widgets\Form\TabsContainer([
                    new CherrySite\Views\Widgets\Form\TabItem('Основные настройки', [
                        new CherrySite\Views\Widgets\Form\Layout('admin.orphans.store', 'POST', [
                            new CherrySite\Views\Widgets\Form\Text($table, 'title', 'Название'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'place', 'Место'),
                            new CherrySite\Views\Widgets\Form\File($table, 'cover', 'Обложка'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_long', 'Описание, полное'),
                            new CherrySite\Views\Widgets\Form\DateTime($table, 'date', 'Дата'),
                            new CherrySite\Views\Widgets\Form\Switcher($table, 'is_active', 'Активен', ['Нет', 'Да']),

                            new CherrySite\Views\Widgets\Form\Text($table, 'url', 'URL (SEO)'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'title_seo', 'Заголовок(SEO)'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'description_seo', 'Описание (SEO)'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'keywords_seo', 'Ключевые слова (SEO)'),
                        ]),
                    ]),
                    new CherrySite\Views\Widgets\Form\TabItemDisabled('Галлерея'),
                ]),
            ])
        );

        return $widget
            ->setRequest($request)
            ->setBuilder($query)
            ->render();
    }

    public function edit(Request $request, Orphan $model)
    {
        $table = $model->getTable();

        $widget = new CherrySite\Views\Common\Page(
            'Редактирование новости помощи детям',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Редактирование новости помощи детям', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.orphans.edit', $model),
                    new CherrySite\Views\Common\DeleteButton('admin.orphans.destroy'),
                ]),
                new CherrySite\Views\Widgets\Form\TabsContainer([
                    new CherrySite\Views\Widgets\Form\TabItem('Основные настройки', [
                        new CherrySite\Views\Widgets\Form\Layout('admin.orphans.update', 'PATCH', [
                            new CherrySite\Views\Widgets\Form\Text($table, 'title', 'Название'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'place', 'Место'),
                            new CherrySite\Views\Widgets\Form\File($table, 'cover', 'Обложка'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'content_long', 'Описание, полное'),
                            new CherrySite\Views\Widgets\Form\DateTime($table, 'date', 'Дата'),
                            new CherrySite\Views\Widgets\Form\Switcher($table, 'is_active', 'Активен', ['Нет', 'Да']),

                            new CherrySite\Views\Widgets\Form\Text($table, 'url', 'URL (SEO)'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'title_seo', 'Заголовок(SEO)'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'description_seo', 'Описание (SEO)'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'keywords_seo', 'Ключевые слова (SEO)'),
                        ]),
                    ]),
                    new CherrySite\Views\Widgets\Form\TabItem('Галлерея', [
                        new CherrySite\Views\Widgets\Form\HasManyToGallery(
                            'photos',
                            'orphans_photos',
                            'path',
                            'position',
                            'admin.orphans_photos.batchStore',
                            'admin.orphans_photos.destroy',
                            'admin.orphans_photos.batchUpdate'
                        ),
                    ]),
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
            'place' => 'nullable|string',
            'cover' => 'required|image',
            'cover__current' => 'nullable|string',
            'content_long' => 'nullable|string',
            'date' => 'required|date_format:d.m.Y H:i:s',
            'url' => 'nullable|string',
            'title_seo' => 'nullable|string',
            'description_seo' => 'nullable|string',
            'keywords_seo' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ], [], [
            'title' => 'Название',
            'place' => 'Место',
            'cover' => 'Обложка',
            'content_long' => 'Описание, полное',
            'date' => 'Дата',
            'url' => 'URL (SEO)',
            'title_seo' => 'Заголовок(SEO)',
            'description_seo' => 'Описание (SEO)',
            'keywords_seo' => 'Ключевые слова (SEO)',
            'is_active' => 'Активен',
        ]);

        $connection = Orphan::resolveConnection();
        $query = Orphan::query();

        $modifier = new CherrySite\Modifiers\Model\Transaction($connection, [
            new CherrySite\Modifiers\Single\Creator($data, $query, [
                new CherrySite\Modifiers\Model\Text('title'),
                new CherrySite\Modifiers\Model\Text('place'),
                new CherrySite\Modifiers\Model\File('cover', null, null, 90, 90),
                new CherrySite\Modifiers\Model\Text('content_long'),
                new CherrySite\Modifiers\Model\Text('url'),
                new CherrySite\Modifiers\Model\Text('title_seo'),
                new CherrySite\Modifiers\Model\Text('description_seo'),
                new CherrySite\Modifiers\Model\Text('keywords_seo'),
                new CherrySite\Modifiers\Model\DateTime('date'),
                new CherrySite\Modifiers\Model\Switcher('is_active'),
            ], new CherrySite\Modifiers\Helpers\Pipe([
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('url', ['id', ['str_slug', 'title']]),
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('title_seo', [['strip_tags', 'title']]),
                $retriever = new CherrySite\Modifiers\Helpers\Retriever(),
            ])),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.orphans.edit', [$retriever->getModel()], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function update(Request $request, Orphan $model)
    {
        $data = $this->validate($request, [
            'title' => 'required|string',
            'place' => 'nullable|string',
            'cover' => 'nullable|image',
            'cover__current' => 'nullable|string',
            'content_long' => 'nullable|string',
            'date' => 'required|date_format:d.m.Y H:i:s',
            'url' => 'nullable|string',
            'title_seo' => 'nullable|string',
            'description_seo' => 'nullable|string',
            'keywords_seo' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ], [], [
            'title' => 'Название',
            'place' => 'Место',
            'cover' => 'Обложка',
            'content_long' => 'Описание, полное',
            'date' => 'Дата',
            'url' => 'URL (SEO)',
            'title_seo' => 'Заголовок(SEO)',
            'description_seo' => 'Описание (SEO)',
            'keywords_seo' => 'Ключевые слова (SEO)',
            'is_active' => 'Активен',
        ]);

        $connection = $model->getConnection();

        $modifier = new CherrySite\Modifiers\Model\Transaction($connection, [
            new CherrySite\Modifiers\Single\Updater($data, $model, [
                new CherrySite\Modifiers\Model\Text('title'),
                new CherrySite\Modifiers\Model\Text('place'),
                new CherrySite\Modifiers\Model\File('cover', null, null, 90, 90),
                new CherrySite\Modifiers\Model\Text('content_long'),
                new CherrySite\Modifiers\Model\Text('url'),
                new CherrySite\Modifiers\Model\Text('title_seo'),
                new CherrySite\Modifiers\Model\Text('description_seo'),
                new CherrySite\Modifiers\Model\Text('keywords_seo'),
                new CherrySite\Modifiers\Model\DateTime('date'),
                new CherrySite\Modifiers\Model\Switcher('is_active'),
            ], new CherrySite\Modifiers\Helpers\Pipe([
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('url', ['id', ['str_slug', 'title']]),
                new CherrySite\Modifiers\Helpers\DefaultValueFromAttributes('title_seo', [['strip_tags', 'title']]),
                $retriever = new CherrySite\Modifiers\Helpers\Retriever(),
            ])),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.orphans.index', [], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function destroy(Request $request, Orphan $model)
    {
        $modifier = new CherrySite\Modifiers\Model\Delete($model);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.orphans.index', [], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }
}