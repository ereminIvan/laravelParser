<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * @var View
     */
    protected $layout = Null;

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout() {

    }

    public function callAction($method, $parameters)
    {
        $this->setupLayout();
        return call_user_func_array(array($this, $method), $parameters);
    }
}
