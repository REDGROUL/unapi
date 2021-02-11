<?php
class Post extends DBconnect
{
    function getContent($url_data)
    {//вызывать метод из названия 
        switch($url_data)
        {
            case 'login':
                $this->login();
            break;
           case 'register':
                $this->register();
            break;
           case 'loginfound':
                $this->loginfound();
            break;
            case 'setOffline':
                $this->setOffline();
            break;
           case 'logOut':
                $this->logOut();
            break;
            case 'setOnline':
                $this->setOnline();
            break;
            case 'cleanHistorySessions':
                $this->cleanHistorySessions();
            break;
            case 'sendMessage':
               $this->sendMessage();
            break; 
            case 'uploadFile':
                $this->uploadFile();
            break; 
            case 'logOutOther':
                $this->logOutOther();
            break;
            case 'changeLogin':
                $this->changeLogin();
            break;
            case 'changePass':
                $this->changePass();
            break;
            case 'changeNick':
                $this->changeNick();
            break;
            case 'report':
                $this->report();
            break;
            case 'createDialog':
                $this->createDialog();
            break;

        }
    }

    function login()
    {
        $login = $_POST['login'];
        $pass = $_POST['password'];
        $token = Gets::getToken();
        $user_agent = Gets::getUserAgent();
        $info = Gets::InfoClient();
        empty($login) ? $error['login'] = 'empty' : $_POST['login'];
        empty($pass) ? $error['password'] = 'empty' : $_POST['password'];
        if(!empty($login) and !empty($pass)){
            $query = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `login` = '$login'");
            if(mysqli_num_rows($query) == 1){
                $result = mysqli_fetch_assoc($query);
                $password_hash = $result['password'];
                $id = $result['id'];
                if(password_verify($pass,$password_hash)){
                    $token = md5(time());
                    mysqli_query($this->connect(), "INSERT INTO `token`(`uid`, `token`, `user_agent`, `info_client`) VALUES ('$id', '$token','$user_agent', '$info')");
                    mysqli_query($this->connect(), "UPDATE `users` SET `status`= 'online', `last_activity` = '0000-00-00 00:00:00' WHERE `id` = '$id'");
                    unset($result['password']);
                    unset($result['last_activity']);
                    unset($result['status']);
                    $result['token'] = $token;
                    Jsons::jsonOutput(true, $result);
                }else{
                    unset($result);
                    Jsons::jsonOutput(false, 'password', 'wrong password');
                }
            }else{
                Jsons::jsonOutput(false, 'login', 'wrong login');
           }
        }else{
            Jsons::jsonOutput(false, $error);
        }
    }

    function register()
    {
        $login = $_POST['login'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nick = $_POST['nick'];
        if(!empty($login) and !empty($pass) and !empty($nick))
        {
            if($this->loginfound($login) == true){
                $query = mysqli_query($this->connect(), "INSERT INTO `users`(`login`, `nick`, `password`, `photo`, `status`, `role`) 
                VALUES ('$login','$nick','$pass','http://api.messenger.com/files/images/standart.jpg','offline','user')");
                if($query == true){
                    Jsons::jsonOutput(true ,"login", "register");
                }else{
                    Jsons::jsonOutput(false,"sql", "sql error");
                }
            }else{
                Jsons::jsonOutput(false,"login", "login exist");
            }
        }else{
            Jsons::jsonOutput(false,"login", "login empty");
        }
    }

    function changeLogin()
    {
        $new_login = $_POST['login'];
        empty($new_login) ? $error['login'] = 'empty' : $_POST['login'];
        if($result = Gets::checkToken()){
            if(!empty($new_login)){
                $old_login = $result['login'];
                if(!empty($new_login)){
                    if($this->loginfound($new_login) == true){
                        mysqli_query($this->connect(), "UPDATE `users` SET `login`= '$new_login' WHERE `login` = '$old_login'");
                        Jsons::jsonOutput(true, "login", "Login change");
                    }else{
                        Jsons::jsonOutput(false, "login","Login used");
                    }
                }
            }else{
                Jsons::jsonOutput(false, 'login','empty');
            }
        }else{
            Jsons::jsonOutput(false, 'login', 'unauth');
        }
    }

    function changePass()
    {
        if($result = Gets::checkToken()){
            $id = $result['id'];
            $db_pass = $result['password'];
            $old_pass = $_POST['old_password'];
            $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            if(!empty($new_pass)){
                if(password_verify($old_pass, $db_pass)){
                    mysqli_query($this->connect(),"UPDATE `users` SET `password`= '$new_pass' WHERE `id` = '$id'");
                }else{
                    Jsons::jsonOutput(false, 'pass', "wrong password");
                }
            }else{
                Jsons::jsonOutput(false, 'pass', "password empty");
            }
        }else{
            Jsons::jsonOutput(false, 'token', 'unauth');
        }
    }

    function changeNick()
    {
    $new_nick = $_POST['nick'];
    empty($new_nick) ? $error['nick'] = "empty" : $_POST['nick'];
    if($result = Gets::checkToken()){
        if(!empty($new_nick)){
            $login = $result['login'];
            if(!empty($new_nick)){
                if($this->loginfound($new_nick) == true){
                    mysqli_query($this->connect(), "UPDATE `users` SET `nick`= '$new_nick' WHERE `login` = '$login'");
                    Jsons::jsonOutput(true, "nick change");
                }
            }
        }else{
            Jsons::jsonOutput(false, 'nick', 'empty');
        }
    }else{
        Jsons::jsonOutput(false, 'token', 'unauth');
    }
    }

    function changePhoto()
    {
        
    }

    function name()
    {
    }
    
    function loginfound($login = null)
    {
        if($login != null){
            $query = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `login` = '$login'");
            if(mysqli_num_rows($query) > 0){
                return false;
            }else{
                return true;
            }
        }else {
            $login = $_POST['login'];
        }
    }

    function photo()
    {
    }

    /**
     * выставление статуса offline
     * можно использовать для постоянного offline'а
     */
    function setOffline()
    {
        if($result = Gets::checkToken()){
            $uid = $result['uid'];
            $date = date("Y-m-d H:i:s");
            mysqli_query($this->connect(), "UPDATE `users` SET `status`= 'offline',`last_activity`= '$date' WHERE `id` = '$uid'");
            Jsons::jsonOutput(true, 'status', "change success");
            return $result['uid'];
        }else{
            Jsons::jsonOutput(false, 'login', 'unauth');
        }
    }
    /**
     * выход из учетки
     */
    function logOut(){
        if($result = Gets::checkToken()){
            $id = $result['uid'];
            $token = $result['token'];
            if($this->setOffline()){
                mysqli_query($this->connect(),"UPDATE `token` SET `token` = 'logout' WHERE `uid` = '$id' AND `token` = '$token'");
                Jsons::jsonOutput(true, 'login', 'logout');
            }
        }else{
            Jsons::jsonOutput(false, 'login', 'unauth');
        }
    }

    /**разлогин всех других устройств */
    function logOutOther()
    {
        if($result = Gets::checkToken()){
            $uid = $result['uid'];
            $token = $result['token'];
            $query = mysqli_query($this->connect(), "UPDATE `token` SET `token`= 'logout' 
            WHERE `uid` = '$uid' AND `token` != '$token'");
            if($query == true){
                Jsons::jsonOutput(true, 'login', "logout other");
            }
        }else{
            Jsons::jsonOutput(false, "Unknown error");
        }
    }

    /**
     * очищает историю сессий
     */
    function cleanHistorySessions()
    {
        if($result = Gets::checkToken()){
            $uid = $result['id'];
            if(mysqli_query($this->connect(), "DELETE FROM `token` WHERE `uid` = '$uid' AND `token` = 'logout'")){
                Jsons::jsonOutput(true, "");
            }
        }else{
            Jsons::jsonOutput(false, 'login', 'unauth');
        }
    }
    /**
     * выставление статуса online
     */
    function setOnline()
    {
        if($result = Gets::checkToken()){
            $uid = $result['id'];
            mysqli_query($this->connect(), "UPDATE `users` SET `status`= 'online', `last_activity` = '0000-00-00 00:00:00' WHERE `id` = '$uid'");
            Jsons::jsonOutput(true, 'status', "change success");
            return $uid;
        }else{
            Jsons::jsonOutput(false, 'token', 'unauth');
            return false;
        }
    }
    /**
     * Загрузка фото, идет проверка по хэшу, если есть в базе то просто вывдает ссылку, иначе
     * загружает 
     */
    /**
     * ПЕРЕДЕЛАТЬ НАХУЙ СРОЧНО!!!!!!
     */
    function uploadFile()
    {         
        if($result = Gets::checkToken()){
            $uid = $result['uid'];
            $uploaddir = 'files/images/';
            $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);    
            $file = uniqid('',true).'.'.explode('.', $uploadfile)[1];
            $sha = hash_file("sha256", $_FILES['userfile']['tmp_name']);
            $query = mysqli_query($this->connect(), "SELECT * FROM `files` WHERE `hash_sum` = '$sha'");
            if(mysqli_num_rows($query) > 0){
                $result = mysqli_fetch_assoc($query);
                $array["name"]=$file;
                $array["path"]=$result['path'];
                $array["datatime_upload"]=$result['time_upload'];
                Jsons::jsonOutput(true, $array);
            }else{
                if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir.$file)) {
                    $path = 'http://'.$_SERVER['SERVER_NAME'].'/'.$uploaddir.$file;
                    $date = date("Y-m-d H:i:s");
                    mysqli_query($this->connect(),"INSERT INTO `files`(`name`, `path`, `owner_id`, `hash_sum`, `time_upload`) 
                    VALUES ('$file','$path','$uid','$sha', '$date')");
                    $array["name"]=$file;
                    $array["path"]=$path;
                    Jsons::jsonOutput(true, $array);
                } else {
                    Jsons::jsonOutput(false,"");
                }
            }
        }
    }

    function report()
    { 
        $message_id = $_POST['message_id'];
        if(!empty($report_id = $_POST['report_user_id']) and !empty($_POST['type_report']) and !empty($_POST['message_id'])){
            if($result = Gets::checkToken()){
                $reporter = $result['id'];
                $message = $_POST['message'];
                $type = $_POST['type_report'];
                if(mysqli_query($this->connect(), "INSERT INTO `report`(`report_user_id`, `reported_user_id`, `message`, `type_report`, `message_id`) 
                VALUES ('$report_id','$reporter','$message','$type', '$message_id')") == true){
                    $array["report_user_id"]=$report_id;
                    $array["reporter"] = $reporter;
                    $array["message_report"]=$message;
                    $array["message_id"]=$message_id;
                    $array["type"]=$type;
                    Jsons::jsonOutput(true, $array);
                }     
            }else{
                Jsons::jsonOutput(false, 'token', 'unauth');
            }
        }else{
            empty($_POST['report_user_id']) ? $err['report_user_id'] = "empty" : $_POST['report_user_id'];
            empty($_POST['type_report']) ? $err['type_report'] = "empty" : $_POST['type_report'];
            empty($_POST['message_id']) ? $err['message_id'] = "empty" : $_POST['message_id'];
            Jsons::jsonOutput(false, $err);
        }    

    }



    function sendMessage()
    {
        $url = $_SERVER['REQUEST_URI'];
        $dialog_id = explode('/', $url)[2];
        $get_user_id = explode('/', $url)[3];
        if($result = Gets::checkToken()){
            $id = $result['id'];
            empty($dialog_id) ? $error['dialog_id'] = "empty" : $dialog_id;
            empty($get_user_id) ? $error['get_user_id'] = "empty" : $get_user_id;
            empty($_POST['message']) ? $error['message'] = "empty" : $message = $_POST['message'];
            $date = date("Y-m-d H:i:s");
            $query = mysqli_query($this->connect(), "SELECT * FROM `dialog` WHERE `dialog_id` = '$dialog_id'");
            if($query == true){
                $result = mysqli_fetch_assoc($query);
                if($get_user_id == $result['one_user_id'] and $get_user_id != $id or $get_user_id == $result['two_user_id'] and $get_user_id != $id){
                    if(!empty($message)){
                        $query = mysqli_query($this->connect(), "INSERT INTO `messages`(`dialog_id`, `message_text`, `sender`, `getter`, `data_send`, `is_read`, `visible_one`, `visible_two`)
                        VALUES ('$dialog_id','$message', '$id', '$get_user_id','$date','0','1','1')");
                        if($query == true){
                            Jsons::jsonOutput(true, "message, get_user, date_send", "'$message', '$get_user_id','$date'");
                        }
                    }else{
                        Jsons::jsonOutput(false, 'message', 'empty');
                    }
                }else{
                    Jsons::jsonOutput(false, 'user','impossible to send');
                }
            }else{
                Jsons::jsonOutput(false, 'dialog', 'not found');
            }
        }else{
            Jsons::jsonOutput(false, 'login', 'unauth');
        }
    }


    /**
     * добавить проверку на существующих диалогов
     * 
     */
    function createDialog()
    {
        if($result = Gets::checkToken()){
            $get_user_id = $_POST['get_user_id'];
            $id = $result['id'];
            if(!empty($get_user_id)){
                $query = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `id` = '$get_user_id'");
                if(mysqli_num_rows($query) == 1){
                    $date = date("Y-m-d H:i:s");
                    $query = mysqli_query($this->connect(), "INSERT INTO `dialog`(`one_user_id`, `two_user_id`, `time_create`) 
                    VALUES ('$id','$get_user_id','$date')");
                    if($query == true){
                        Jsons::jsonOutput(true, "dialog", "created");
                    }
                }else{
                    Jsons::jsonOutput(false, "dialog", "created");

                }
            }
        }else{
            Jsons::jsonOutput(false, "dialog", "created");

        }
    }

    function AgressivePush()
    {
    }

    function setRuleAgressPush()
    {
    }
}