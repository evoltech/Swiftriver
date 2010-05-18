<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ContentList extends Controller_Template_Master
{
    private $state;

    public function action_index($state)
    {
        $this->action_get($state);
    }

    public function action_get($state)
    {
        $this->state = $state;
        $this->set_content();
        $this->set_menu();
    }

    private function set_menu()
    {
        $this->template->menu = new View("parts/contentmenu");
        $this->template->menu->state = $this->state;
        $this->template->menu->new_content_class = ($this->state == "new_content") ? "selected" : "";
        $this->template->menu->accurate_class = ($this->state == "accurate") ? "selected" : "";
        $this->template->menu->inaccurate_class = ($this->state == "inaccurate") ? "selected" : "";
        $this->template->menu->chatter_class = ($this->state == "chatter") ? "selected" : "";
    }

    private function set_content()
    {
        $this->template->content = new View("pages/contentlist");
        $this->template->content->state = $this->state;
    }
} 
