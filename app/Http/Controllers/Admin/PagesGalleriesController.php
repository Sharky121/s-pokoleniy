<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageGallery;
use Illuminate\Http\Request;
use Cherryline\CherrySite;

class PagesGalleriesController extends Controller
{
    public function index(Request $request, Page $parent)
    {
        $model = new PageGallery();
        $table = $model->getTable();
        $query = $model->newQuery()
            ->where('page_id', $parent->id);

        $widget = new CherrySite\Views\Common\Page(
            'Галлереи',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Галлереи', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.pages.pages_galleries.index', $parent),
                    new CherrySite\Views\Common\CreateButton(['admin.pages.pages_galleries.create', [$parent]]),
                ]),
                new CherrySite\Views\Widgets\Table\Layout([
                    new CherrySite\Views\Widgets\Table\Text($table, 'id', 'ID'),
                    new CherrySite\Views\Widgets\Table\Text($table, 'key', 'Ключевое слово'),
                    new CherrySite\Views\Widgets\Table\Numeric($table, 'position', 'Сортировка'),
                    new CherrySite\Views\Widgets\Table\Actions('Действия', [
                        new CherrySite\Views\Widgets\Table\EditButton(['admin.pages.pages_galleries.edit', [$parent]]),
                        new CherrySite\Views\Widgets\Table\DeleteButton(['admin.pages.pages_galleries.destroy', [$parent]]),
                    ]),
                ]),
            ])
        );

        return $widget
            ->setRequest($request)
            ->setBuilder($query)
            ->render();
    }

    public function create(Request $request, Page $parent)
    {
        $model = new PageGallery();
        $table = $model->getTable();
        $query = $model->newQuery()
            ->where('page_id', $parent->id);

        $widget = new CherrySite\Views\Common\Page(
            'Новая галлерея',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Новая галлерея', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.pages.pages_galleries.create', $parent),
                ]),
                new CherrySite\Views\Widgets\Form\TabsContainer([
                    new CherrySite\Views\Widgets\Form\TabItem('Основные настройки', [
                        new CherrySite\Views\Widgets\Form\Layout(['admin.pages.pages_galleries.store', [$parent]], 'POST', [
                            new CherrySite\Views\Widgets\Form\Text($table, 'key', 'Ключевое слово'),
                            new CherrySite\Views\Widgets\Form\Numeric($table, 'position', 'Сортировка'),
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

    public function edit(Request $request, Page $parent, PageGallery $model)
    {
        $table = $model->getTable();

        $widget = new CherrySite\Views\Common\Page(
            'Редактирование галлереи',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Редактирование галлереи', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.pages.pages_galleries.edit', $parent, $model),
                    new CherrySite\Views\Common\DeleteButton(['admin.pages.pages_galleries.destroy', [$parent]]),
                ]),
                new CherrySite\Views\Widgets\Form\TabsContainer([
                    new CherrySite\Views\Widgets\Form\TabItem('Основные настройки', [
                        new CherrySite\Views\Widgets\Form\Layout(['admin.pages.pages_galleries.update', [$parent]], 'PATCH', [
                            new CherrySite\Views\Widgets\Form\Text($table, 'key', 'Ключевое слово'),
                            new CherrySite\Views\Widgets\Form\Numeric($table, 'position', 'Сортировка'),
                        ]),
                    ]),
                    new CherrySite\Views\Widgets\Form\TabItem('Галлерея', [
                        new CherrySite\Views\Widgets\Form\HasManyToGallery(
                            'photos',
                            'pages_galleries_photos',
                            'path',
                            'position',
                            'admin.pages.pages_galleries.photos.batchStore',
                            'admin.pages.pages_galleries.photos.destroy',
                            'admin.pages.pages_galleries.photos.batchUpdate',
                            [$parent],
                            [$parent],
                            [$parent]
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

    public function store(Request $request, Page $parent)
    {
        $data = $this->validate($request, [
            'key' => 'required|string',
            'position' => 'nullable|integer',
        ], [], [
            'key' => 'Ключевое слово',
            'position' => 'Сортировка',
        ]);

        $connection = PageGallery::resolveConnection();
        $query = PageGallery::query();

        $modifier = new CherrySite\Modifiers\Model\Transaction($connection, [
            new CherrySite\Modifiers\Single\Creator($data, $query, [
                new CherrySite\Modifiers\Model\Value('page_id', $parent->id),
                new CherrySite\Modifiers\Model\Text('key'),
                new CherrySite\Modifiers\Model\Numeric('position'),
            ], new CherrySite\Modifiers\Helpers\Pipe([
                $retriever = new CherrySite\Modifiers\Helpers\Retriever(),
            ])),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.pages.pages_galleries.edit', [$parent, $retriever->getModel()], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function update(Request $request, Page $parent, PageGallery $model)
    {
        $data = $this->validate($request, [
            'key' => 'required|string',
            'position' => 'nullable|integer',
        ], [], [
            'key' => 'Ключевое слово',
            'position' => 'Сортировка',
        ]);

        $connection = $model->getConnection();

        $modifier = new CherrySite\Modifiers\Model\Transaction($connection, [
            new CherrySite\Modifiers\Single\Updater($data, $model, [
                new CherrySite\Modifiers\Model\Text('key'),
                new CherrySite\Modifiers\Model\Numeric('position'),
            ], new CherrySite\Modifiers\Helpers\Pipe([
                $retriever = new CherrySite\Modifiers\Helpers\Retriever(),
            ])),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.pages.pages_galleries.index', [$parent], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function destroy(Request $request, Page $parent, PageGallery $model)
    {
        $modifier = new CherrySite\Modifiers\Model\Delete($model);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.pages.pages_galleries.index', [$parent], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }
}