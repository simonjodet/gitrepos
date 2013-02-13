<?php
namespace Gitrepos\Controllers;

use \Silex\Application,
    \Symfony\Component\HttpFoundation\Request;

class Controller
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;


    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
        $this->validator = $this->app['validator'];
    }
}
