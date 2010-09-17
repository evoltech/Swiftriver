<?php
class Introduction implements IInstallStep
{	
    public function GetDescription()
    {
        return "Welcome to the installer for the SwiftRiver Sweeper Application.<br/>  ".
               "If you haven't already, why don't you check out ".
               "the <strong>Install Guide</strong> on the swiftly.org website...<br/> ".
               "<a href='?position=1'><img src='assets/images/button-letsgetstarted.png' /></a>";
    }

    public function GetName()
    {
        return "Introduction";
    }

    public function RunChecks($postVar)
    {
        return null;
    }

    public function Render()
    {

    }
}
?>
