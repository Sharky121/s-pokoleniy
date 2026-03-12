<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Cherryline\CherrySite;

class PartnersController extends Controller
{
    public function index(Request $request)
    {
        $model = new Partner();
        $table = $model->getTable();
        $query = $model->newQuery()->orderBy('position', 'desc');
        $paginator = $query->simplePaginate(20);

        $widget = new CherrySite\Views\Common\Page(
            'Партнёры',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Партнёры', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.partners.index'),
                    new CherrySite\Views\Common\CreateButton('admin.partners.create'),
                ]),
                new CherrySite\Views\Widgets\Table\Layout([
                    new CherrySite\Views\Widgets\Table\Text($table, 'id', 'ID'),
                    new CherrySite\Views\Widgets\Table\Text($table, 'title', 'Название'),
                    new CherrySite\Views\Widgets\Table\Switcher($table, 'is_active', 'Активен', ['Нет', 'Да']),
                    new CherrySite\Views\Widgets\Table\Actions('Действия', [
                        new CherrySite\Views\Widgets\Table\EditButton('admin.partners.edit'),
                        new CherrySite\Views\Widgets\Table\DeleteButton('admin.partners.destroy'),
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
        $model = new Partner();
        $table = $model->getTable();
        $query = $model->newQuery();

        $widget = new CherrySite\Views\Common\Page(
            'Новый партнёр',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Новый партнёр', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.partners.create'),
                ]),
                new CherrySite\Views\Widgets\Form\TabsContainer([
                    new CherrySite\Views\Widgets\Form\TabItem('Основные настройки', [
                        new CherrySite\Views\Widgets\Form\Layout('admin.partners.store', 'POST', [
                            new CherrySite\Views\Widgets\Form\Text($table, 'title', 'Название'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'site', 'Сайт'),
                            new CherrySite\Views\Widgets\Form\File($table, 'cover', 'Обложка'),
                            new CherrySite\Views\Widgets\Form\Numeric($table, 'position', 'Сортировка'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'description', 'Описание'),
                            new CherrySite\Views\Widgets\Form\Switcher($table, 'is_active', 'Активен', ['Нет', 'Да']),
                        ]),
                    ]),
                ]),
            ])
        );

        return $widget
            ->setRequest($request)
            ->setBuilder($query)
            ->render();
    }

    public function edit(Request $request, Partner $model)
    {
        $table = $model->getTable();

        $widget = new CherrySite\Views\Common\Page(
            'Редактирование партнёра',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Редактирование партнёра', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.partners.edit', $model),
                    new CherrySite\Views\Common\DeleteButton('admin.partners.destroy'),
                ]),
                new CherrySite\Views\Widgets\Form\TabsContainer([
                    new CherrySite\Views\Widgets\Form\TabItem('Основные настройки', [
                        new CherrySite\Views\Widgets\Form\Layout('admin.partners.update', 'PATCH', [
                            new CherrySite\Views\Widgets\Form\Text($table, 'title', 'Название'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'site', 'Сайт'),
                            new CherrySite\Views\Widgets\Form\File($table, 'cover', 'Обложка'),
                            new CherrySite\Views\Widgets\Form\Numeric($table, 'position', 'Сортировка'),
                            new CherrySite\Views\Widgets\Form\Wysiwyg($table, 'description', 'Описание'),
                            new CherrySite\Views\Widgets\Form\Switcher($table, 'is_active', 'Активен', ['Нет', 'Да']),
                        ]),
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
            'site' => 'nullable|string',
            'cover' => 'required|image',
            'cover__current' => 'nullable|string',
            'position' => 'nullable|integer',
            'description' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ], [], [
            'title' => 'Название',
            'site' => 'Сайт',
            'cover' => 'Обложка',
            'position' => 'Сортировка',
            'description' => 'Описание',
            'is_active' => 'Активен',
        ]);

        $connection = Partner::resolveConnection();
        $query = Partner::query();

        $modifier = new CherrySite\Modifiers\Model\Transaction($connection, [
            new CherrySite\Modifiers\Single\Creator($data, $query, [
                new CherrySite\Modifiers\Model\Text('title'),
                new CherrySite\Modifiers\Model\Text('site'),
                new CherrySite\Modifiers\Model\File('cover', null, null, 90, 90),
                new CherrySite\Modifiers\Model\Numeric('position'),
                new CherrySite\Modifiers\Model\Text('description'),
                new CherrySite\Modifiers\Model\Switcher('is_active'),
            ], new CherrySite\Modifiers\Helpers\Pipe([
                $retriever = new CherrySite\Modifiers\Helpers\Retriever(),
            ])),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.partners.edit', [$retriever->getModel()], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function update(Request $request, Partner $model)
    {
        $data = $this->validate($request, [
            'title' => 'required|string',
            'site' => 'nullable|string',
            'cover' => 'nullable|image',
            'cover__current' => 'nullable|string',
            'position' => 'nullable|integer',
            'description' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ], [], [
            'title' => 'Название',
            'site' => 'Сайт',
            'cover' => 'Обложка',
            'position' => 'Сортировка',
            'description' => 'Описание',
            'is_active' => 'Активен',
        ]);

        $connection = $model->getConnection();

        $modifier = new CherrySite\Modifiers\Model\Transaction($connection, [
            new CherrySite\Modifiers\Single\Updater($data, $model, [
                new CherrySite\Modifiers\Model\Text('title'),
                new CherrySite\Modifiers\Model\Text('site'),
                new CherrySite\Modifiers\Model\File('cover', null, null, 90, 90),
                new CherrySite\Modifiers\Model\Numeric('position'),
                new CherrySite\Modifiers\Model\Text('description'),
                new CherrySite\Modifiers\Model\Switcher('is_active'),
            ], new CherrySite\Modifiers\Helpers\Pipe([
                $retriever = new CherrySite\Modifiers\Helpers\Retriever(),
            ])),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.partners.index', [], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function destroy(Request $request, Partner $model)
    {
        $modifier = new CherrySite\Modifiers\Model\Delete($model);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.partners.index', [], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }
}
