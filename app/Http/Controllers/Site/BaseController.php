<?php

namespace App\Http\Controllers\Site;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Mail\Mailer;
use App\Http\Controllers\Controller;
use Illuminate\Container\Container;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $container;
    protected $view;
    protected $response;
    protected $mailer;

    public function __construct(Container $container, Factory $view, ResponseFactory $response, Mailer $mailer)
    {
        $this->container = $container;
        $this->view = $view;
        $this->response = $response;
        $this->mailer = $mailer;
    }
}