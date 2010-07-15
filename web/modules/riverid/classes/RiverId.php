<?php
class RiverId {
    /**
     * This function checks that the current user is logged in.
     * The return value is an associative array in the following
     * format:
     * array(
     *  "IsLoggedIn" => bool,
     *  "Role" => string
     * );
     * @return array(bool,role)
     */
    public static function is_logged_in()
    {
        $key = $_SESSION["authkey"];

        $username = $_SESSION["username"];

        if($key == null || $key == "" || $username == null || $username == "") 
            return array("IsLoggedIn" => false);

        $result = self::get_hashes_password_and_role($username);

        if($result === false)
            return array("IsLoggedIn" => false);;

        $password = $result["password"];

        if($password != $key)
            return array("IsLoggedIn" => false);

        $role = $result["role"];

        $return = array(
            "IsLoggedIn" => true,
            "Role" => $role
        );

        return $return;
    }

    private static function get_hashes_password_and_role($username)
    {
        $con = mysql_connect(
                Config_RiverId::$databaseurl,
                Config_RiverId::$username,
                Config_RiverId::$password);

        mysql_select_db(Config_RiverId::$database, $con);

        mysql_query(Config_RiverId::$createsql, $con);

        $username = mysql_escape_string($username);

        $sql = "SELECT * FROM users WHERE username = '" . $username . '";';

        $results = mysql_query($sql);

        $row = mysql_fetch_assoc($results);

        if($row == false)
            return false;

        $role = $row["role"];

        $password = $row["password"];

        $hashedPassword = md5($password);

        $return = array(
            "password" => $hashedPassword,
            "role" => $role
        );

        return $return;
    }
}
?>
