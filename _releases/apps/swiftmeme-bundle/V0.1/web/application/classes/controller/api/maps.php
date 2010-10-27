<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Maps extends Controller
{
    public $widgetid = '';

    public function action_getmap($widgetid)
    {
        $map = new View("parts/mapwidget");

        // Render the graph
        $this->$widgetid = $widgetid;
        $this->request->response = $map;
    }
}