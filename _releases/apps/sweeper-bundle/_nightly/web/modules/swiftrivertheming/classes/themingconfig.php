<?php
class ThemingConfig
{
    public static $databaseurl = 'localhost';
    public static $username = 'sweeper_test';
    public static $password = 'sweeper_test';
    public static $database = 'sweeper_test';

    public static $createsql = "CREATE TABLE IF NOT EXISTS theming ( theme VARCHAR(2000) ) TYPE=innodb";
}
?>