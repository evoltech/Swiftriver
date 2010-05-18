<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config_Themes extends Controller_Template_Modal
{
    public function action_index()
    {
        $this->template->title = "Choose a theme";
        $this->template->content = new View("config/themes");
        $this->template->content->themes = $this->collect_themes();
    }

    public function action_select()
    {
        //TODO: clean and validate post variables
        $returnUrl = $_POST["currenturl"];
        $fileName = $_POST["cssfile"];
        Cookie::set("theme", $fileName);
        $this->request->redirect("");
    }

    private function collect_themes()
    {
        $themes = array();

        try
        {
            $dir = DOCROOT."/themes";
            $dirItterator = new \DirectoryIterator($dir);
            foreach($dirItterator as $directory) {
                if($directory->isDir()) {
                    $dirname = $directory->getFilename();
                    $innerItterator = new DirectoryIterator($directory->getPathname());
                    foreach($innerItterator as $file) {
                        if($file->isFile()) {
                            $filePath = $file->getPathname();
                            $fileName = $file->getFilename();
                            if($fileName == "style.css") {
                                $theme->cssFilePath = url::base()."themes/".$dirname."/style.css";
                                $theme->thumbnail = str_replace("style.css", "thumbnail.png", $filePath);
                                $theme->title = "";
                                $theme->description = "";
                                $theme->author = "";
                                $theme->email = "";
                                $theme->url = "";
                                $theme->notes = "";
                                $file = file($filePath);
                                foreach($file as $line) {
                                    if(strpos($line, "@title") != 0) {
                                        $theme->title = trim(substr($line, strpos($line, "@title") + 6));
                                    }
                                    else if (strpos($line, "@description") != 0) {
                                        $theme->description = trim(substr($line, strpos($line, "@description") + 12));
                                    }
                                    else if (strpos($line, "@author") != 0) {
                                        $theme->author = trim(substr($line, strpos($line, "@author") + 7));
                                    }
                                    else if (strpos($line, "@email") != 0) {
                                        $theme->email = trim(substr($line, strpos($line, "@email") + 6));
                                    }
                                    else if (strpos($line, "@url") != 0) {
                                        $theme->url = trim(substr($line, strpos($line, "@url") + 4));
                                    }
                                    else if (strpos($line, "@notes") != 0) {
                                        $theme->notes = trim(substr($line, strpos($line, "@notes") + 6));
                                    }
                                }
                                $themes[] = $theme;
                            }
                        }
                    }
                }
            }
        }
        catch (Exception $e)
        {

        }

        return $themes;
    }
}
