<?php

namespace App\TemplatesParameters;

use App\Models\Page;

class Link
{
    /**
     * @var \App\Models\Page
     */
    private $page;

    private $parameters;

    private $anchor;

    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->parameters = [];
        $this->anchor = '';
    }

    public function __get($parameter)
    {
        if (strpos($parameter, '#') === 0) {
            $this->anchor = $parameter;
            return $this;
        }

        $this->parameters[] = $parameter;
        return $this;
    }

    public function __toString()
    {
        return route("pages.{$this->page->id}", $this->parameters) . $this->anchor;
    }

    public function __isset($name)
    {
        return true;
    }
}