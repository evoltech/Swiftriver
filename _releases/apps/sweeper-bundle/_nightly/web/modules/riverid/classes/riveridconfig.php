<?php
class RiverIdConfig
{
    public static $databaseurl = 'localhost';
    public static $username = 'sweeper_test';
    public static $password = 'sweeper_test';
    public static $database = 'sweeper_test';

    public static $createsql = "CREATE TABLE IF NOT EXISTS users ( username VARCHAR(2000), password VARCHAR(2000), role VARCHAR(2000) ) TYPE=innodb";
}