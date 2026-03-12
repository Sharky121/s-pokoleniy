<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orphan;
use App\Models\OrphanPhoto;
use Illuminate\Http\Request;

use Cherryline\CherrySite\Modifiers\{
    Model\Transaction as ModelTransaction,
    Model\Delete as ModelDelete,
    Model\File as ModelFile,
    Model\Simple as ModelSimple,
    Batch\Updater as BatchUpdater,
    Batch\Creator as BatchCreator
};

class OrphansPhotosController extends Controller
{
    public function batchStore(Request $request, Orphan $parent)
    {
        $connection = $parent->getConnection();
        $query = $parent->photos();

        // Приводим массив к нужному виду
        $data = [];
        foreach ($request->file('photos') as $i => $image) {
            $data[] = ['path' => $image, 'position' => $i];
        }

        $modifier = new ModelTransaction($connection, [
            new BatchCreator($data, $query, [
                new ModelFile('path', null, null, 90, 90),
                new ModelSimple('position'),
            ]),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('messages', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.orphans.edit', [$parent], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function batchUpdate(Request $request, Orphan $parent)
    {
        $connection = $parent->getConnection();
        $query = $parent->photos();

        $modifier = new ModelTransaction($connection, [
            new BatchUpdater($request->input('photos'), $query, [
                new ModelSimple('position'),
            ]),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('messages', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.orphans.edit', [$parent], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function destroy(Request $request, Orphan $parent, OrphanPhoto $model)
    {
        $modifier = new ModelDelete($model);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('messages', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.orphans.edit', [$parent], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }
}