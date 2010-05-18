<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Template_Master extends Controller_Template
{
    public $template = 'template/master';

    public function before()
    {
        parent::before();
        
        $this->template->title = 'Swiftriver';
        $this->template->header = new View('defaults/header');
        $this->template->content = '';
        $this->template->rightbar = new View('defaults/rightbar');
        $this->template->footer = new View('defaults/footer');
    }

}

