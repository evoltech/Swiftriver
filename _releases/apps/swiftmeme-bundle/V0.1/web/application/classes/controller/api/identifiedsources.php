<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_IdentifiedSources extends Controller
{
    public function action_getsources()
    {
        $widget = new View("parts/identifiedsourceswidget");

        // Render the widget
        $this->request->response = $widget;
    }
}