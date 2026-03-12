<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\TemplatesParameters\LinksFactory;

class PagesController extends BaseController
{
    private $parameters = [];

    public function index(Request $request)
    {
        $pages = app()->make('pages');

        if ($request->e instanceof HttpExceptionInterface) {
            // Страница не найдена
            $status = 404;
            $this->parameters = [];
            $currentPage = $pages->where('id', 2)->first();
        } else {
            $status = 200;
            $route = $request->route();
            $this->parameters = $route->parameters();
            $currentPage = $route->getAction()['page'];
        }

        // Дополняем параметры
        $this->parameters['currentYear'] = date('Y');
        $this->parameters['links'] = app()->make(LinksFactory::class, ['pages' => $pages]);

        return $this->response->make(
            $this->view->make($currentPage->view, [
                    'layout' => $pages->where('id', 1)->first(),
                    'currentPage' => $currentPage,
                    'sub' => [$this, 'sub'],
                ] + $this->parameters),
            $status
        );
    }

    public function sub(?string $text): string
    {
        if (is_null($text) || empty($text)) {
            return '';
        }

        return preg_replace_callback('#{([^}\s]+)}#u', function ($matches) {
            return data_get($this->parameters, $matches[1], '');
        }, $text);
    }
}