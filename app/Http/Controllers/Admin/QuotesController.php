<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;
use Cherryline\CherrySite;

class QuotesController extends Controller
{
    public function index(Request $request)
    {
        $model = new Quote();
        $table = $model->getTable();
        $query = $model->newQuery()->orderBy('id', 'desc');
        $paginator = $query->simplePaginate(20);

        $widget = new CherrySite\Views\Common\Page(
            'Цитаты',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Цитаты', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.quotes.index'),
                    new CherrySite\Views\Common\CreateButton('admin.quotes.create'),
                ]),
                new CherrySite\Views\Widgets\Table\Layout([
                    new CherrySite\Views\Widgets\Table\Text($table, 'id', 'ID'),
                    new CherrySite\Views\Widgets\Table\Text($table, 'author', 'Автор'),
                    new CherrySite\Views\Widgets\Table\Switcher($table, 'is_active', 'Активен', ['Нет', 'Да']),
                    new CherrySite\Views\Widgets\Table\Actions('Действия', [
                        new CherrySite\Views\Widgets\Table\EditButton('admin.quotes.edit'),
                        new CherrySite\Views\Widgets\Table\DeleteButton('admin.quotes.destroy'),
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
        $model = new Quote();
        $table = $model->getTable();
        $query = $model->newQuery();

        $widget = new CherrySite\Views\Common\Page(
            'Новая цитата',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Новая цитата', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.quotes.create'),
                ]),
                new CherrySite\Views\Widgets\Form\TabsContainer([
                    new CherrySite\Views\Widgets\Form\TabItem('Основные настройки', [
                        new CherrySite\Views\Widgets\Form\Layout('admin.quotes.store', 'POST', [
                            new CherrySite\Views\Widgets\Form\Text($table, 'author', 'Автор'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'content', 'Цитата'),
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

    public function edit(Request $request, Quote $model)
    {
        $table = $model->getTable();

        $widget = new CherrySite\Views\Common\Page(
            'Редактирование цитаты',
            new CherrySite\Views\Common\Navbar(),
            new CherrySite\Views\Common\Container([
                new CherrySite\Views\Common\Panel('Редактирование цитаты', [
                    new CherrySite\Views\Common\Breadcrumbs('admin.quotes.edit', $model),
                    new CherrySite\Views\Common\DeleteButton('admin.quotes.destroy'),
                ]),
                new CherrySite\Views\Widgets\Form\TabsContainer([
                    new CherrySite\Views\Widgets\Form\TabItem('Основные настройки', [
                        new CherrySite\Views\Widgets\Form\Layout('admin.quotes.update', 'PATCH', [
                            new CherrySite\Views\Widgets\Form\Text($table, 'author', 'Автор'),
                            new CherrySite\Views\Widgets\Form\Text($table, 'content', 'Цитата'),
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
            'author' => 'required|string',
            'content' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ], [], [
            'author' => 'Автор',
            'content' => 'Цитата',
            'is_active' => 'Активен',
        ]);

        $connection = Quote::resolveConnection();
        $query = Quote::query();

        $modifier = new CherrySite\Modifiers\Model\Transaction($connection, [
            new CherrySite\Modifiers\Single\Creator($data, $query, [
                new CherrySite\Modifiers\Model\Text('author'),
                new CherrySite\Modifiers\Model\Text('content'),
                new CherrySite\Modifiers\Model\Switcher('is_active'),
            ], new CherrySite\Modifiers\Helpers\Pipe([
                $retriever = new CherrySite\Modifiers\Helpers\Retriever(),
            ])),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.quotes.edit', [$retriever->getModel()], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function update(Request $request, Quote $model)
    {
        $data = $this->validate($request, [
            'author' => 'required|string',
            'content' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ], [], [
            'author' => 'Автор',
            'content' => 'Цитата',
            'is_active' => 'Активен',
        ]);

        $connection = $model->getConnection();

        $modifier = new CherrySite\Modifiers\Model\Transaction($connection, [
            new CherrySite\Modifiers\Single\Updater($data, $model, [
                new CherrySite\Modifiers\Model\Text('author'),
                new CherrySite\Modifiers\Model\Text('content'),
                new CherrySite\Modifiers\Model\Switcher('is_active'),
            ], new CherrySite\Modifiers\Helpers\Pipe([
                $retriever = new CherrySite\Modifiers\Helpers\Retriever(),
            ])),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.quotes.index', [], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function destroy(Request $request, Quote $model)
    {
        $modifier = new CherrySite\Modifiers\Model\Delete($model);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('success', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.quotes.index', [], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }
}