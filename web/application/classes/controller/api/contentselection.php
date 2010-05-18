<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_ContentSelection extends Controller
{
    public function action_get($state, $pagestart, $pagesize, $minveracity = 0, $maxveracity = 100)
    {
        $json_encoded_parameters = json_encode(array(
            "state" => $state,
            "pagesize" => $pagesize,
            "pagestart" => $pagestart,
            "minVeracity" => $minveracity,
            "maxVeracity" => $maxveracity,
        ));
        $json = API::content_api()->get_paged_content_by_state_and_source_veracity($json_encoded_parameters);
        $this->request->response = $json;
    }
}