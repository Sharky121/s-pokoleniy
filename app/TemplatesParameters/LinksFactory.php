<?php

namespace App\TemplatesParameters;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class LinksFactory
{
    /**
     * @var App\Models\Page[]
     */
    private $pages;

    public function __construct($pages)
    {
        $this->pages = $pages;
    }

    public function __get($id)
    {
        $page = $this->pages->where('id', $id)->first();
        if (!$page) {
            throw new ModelNotFoundException();
        }

        return new Link($page);
    }

    public function __isset($name)
    {
        return true;
    }
}