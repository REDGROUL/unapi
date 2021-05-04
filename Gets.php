<?php
class Gets
{
    static function getToken()
    {
        $header = getallheaders();
        $token = $header['token'];
        return $token; 
    }

    static function getUserAgent()
    {
        $header = getallheaders();
        $user_agent = $header['User-Agent']; 
        return $user_agent; 
    }
    static function InfoClient()
    {
        $header = getallheaders();
        $info = $header['Info-Client']; 
        return $info; 
    }

    static function checkToken()
    {
        $db = new DBconnect();
        $token = Self::getToken();
        $query = mysqli_query($db ->connect(), "SELECT * FROM `token` INNER JOIN `users` ON token.uid = users.id WHERE `token` = '$token'");
        if(mysqli_num_rows($query) == 1){
             return mysqli_fetch_assoc($query);
        }else{
            return false;
        }
    }

    

}