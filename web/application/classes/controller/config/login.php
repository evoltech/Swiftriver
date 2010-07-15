<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_Login extends Controller_Template_Modal
{
    public function action_index()
    {
        $this->template->title = "Login";
        $this->template->content = new View('config/login');
    }
}
