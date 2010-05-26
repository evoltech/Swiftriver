<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_Turbines extends Controller_Template_Modal
{
    public function action_index()
    {
        $this->template->title = "Configure Swiftriver Turbines";
        $this->template->content = new View("config/turbines");
        $return = API::preprocessing_steps_api()->list_all_preprocessing_steps();
        $object = json_decode($return);
        $this->template->content->turbines = $object->data->steps;
    }

    public function action_activate()
    {
        $object->name = $_POST["name"];
        $json_encodedParameters = json_encode($object);
        $json = API::preprocessing_steps_api()->activate_preprocessing_step($json_encodedParameters);
        $this->request->response = $json;
    }

    public function action_deactivate()
    {
        $object->name = $_POST["name"];
        $json_encodedParameters = json_encode($object);
        $json = API::preprocessing_steps_api()->deactivate_preprocessing_step($json_encodedParameters);
        $this->request->response = $json;
    }
}