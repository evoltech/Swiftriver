<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_FilterView extends Controller
{
    public function action_getfilters()
    {
        $widget = new View("parts/filterviewwidget");

        // Render the widget
        $this->request->response = $widget;
    }
}