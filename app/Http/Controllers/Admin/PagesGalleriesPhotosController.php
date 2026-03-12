<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageGallery;
use App\Models\PageGalleryPhoto;
use Illuminate\Http\Request;

use Cherryline\CherrySite\Modifiers\{
    Model\Transaction as ModelTransaction,
    Model\Delete as ModelDelete,
    Model\File as ModelFile,
    Model\Simple as ModelSimple,
    Batch\Updater as BatchUpdater,
    Batch\Creator as BatchCreator
};

class PagesGalleriesPhotosController extends Controller
{
    public function batchStore(Request $request, Page $page, PageGallery $parent)
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
                new ModelFile('path', null, null, 217, 144),
                new ModelSimple('position'),
            ]),
        ]);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('messages', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.pages.pages_galleries.edit', [$page, $parent], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function batchUpdate(Request $request, Page $page, PageGallery $parent)
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
                ->json(['redirectTo' => route('admin.pages.pages_galleries.edit', [$page, $parent], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }

    public function destroy(Request $request, Page $page, PageGallery $parent, PageGalleryPhoto $model)
    {
        $modifier = new ModelDelete($model);

        $result = $modifier->run();
        if ($result->succeeded()) {
            $request->session()->flash('messages', $result->messages());
            return response()
                ->json(['redirectTo' => route('admin.pages.pages_galleries.edit', [$page, $parent], false)]);
        }

        return response()
            ->json(['errors' => $result->messages()]);
    }
}