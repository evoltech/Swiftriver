<?php
interface IInstallStep
{
    public function GetName();

    public function GetDescription();

    public function RunChecks();

    public function Render();
}
?>
