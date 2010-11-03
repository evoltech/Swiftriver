<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_TermsToMonitor extends Controller
{
    public function action_getterms()
    {
        $widget = new View("parts/termstomonitorwidget");

        // Render the widget
        $this->request->response = $widget;
    }
}