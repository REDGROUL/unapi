<?php

class Get extends DBconnect
{

    function getContent($url_data)
    {
        $method = explode("=", $url_data);
        
        switch ($method[0])
        {
            case "getInfo":
                $this->getInfo($url_data);
            break;
            case "getDialogs":
                $this->getDialogs($url_data);
            break;
            case "getMessage":
                $this->getMessage($url_data);
            break;
            case "search":
                $this->search($url_data);
            break;
            
        }
    }


    function getInfo()
    {
        if($result = Gets::checkToken()){
            $url = $_SERVER['REQUEST_URI'];
            $id = explode('/', $url)[2];
            if(isset($id) and $id!=""){
                $query = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `id` = '$id'");
                if(mysqli_num_rows($query) == 1){
                $result = mysqli_fetch_assoc($query);
                unset($result['password']);
                unset($result['token']);
                unset($result['uid']);
                unset($result['user_agent']);
                unset($result['info_client']);
                Jsons::jsonOutput(true, $result);
                }else{
                    Jsons::jsonOutput(false, 'id','user not found');
                }
            }else{
                unset($result['password']);
                unset($result['token']);
                unset($result['uid']);
                unset($result['user_agent']);
                unset($result['info_client']);
                Jsons::jsonOutput(true, $result);
            }
        }else{
            Jsons::jsonOutput(false, 'login','unauth');
        }

    }
    /**
     * 
     * todo
     * дополение данных инфой польователй
     * убрать дублирование диалогов
     * 
     * 
     */
    function getDialogs()
    {
        if($result = Gets::checkToken()){

            $uid = $result['uid'];
            $query = mysqli_query($this->connect(), "SELECT * FROM `dialog` WHERE `one_user_id` = '$uid' OR `two_user_id` = '$uid'");
            while($row=mysqli_fetch_assoc($query)){                    
               $data[] = $row;
            }
            Jsons::jsonOutput(true, $data);
        }else{
            Jsons::jsonOutput(false, 'login','unauth');
        }
    }

    function getMessage()
    {
        if(Gets::checkToken()){
            $url = $_SERVER['REQUEST_URI'];
            $dialog_id = explode('/', $url)[2];
            $date = date("2021-02-06");
            if(!empty($dialog_id)){
                $query = mysqli_query($this->connect(), "SELECT * FROM `messages` WHERE `dialog_id` = '$dialog_id' AND DATE(data_send) = '$date'");
                if($query == true){
                    while($row=mysqli_fetch_assoc($query)){                    
                        $data[] = $row;
                    }
                    if(!empty($data)){
                        Jsons::jsonOutput(true, $data);
                    }else{
                        Jsons::jsonOutput(false, "dialog", "access denied");
                    }
                }else{
                    Jsons::jsonOutput(false, "message", "some problem");
                }
            }else{
                Jsons::jsonOutput(false, 'dialog_id', 'not exist');
            }
            
        }else{}
    }

    function search()
    {
        if(Gets::checkToken()){
            $url = $_SERVER['REQUEST_URI'];
            $login = explode('/', $url)[2];
            empty($login) ? $error['login'] = "empty" : $login;
            if(!empty($login)){
                $query = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `login` = '$login'");
                if(mysqli_num_rows($query) == 1){
                    $result = mysqli_fetch_assoc($query);
                    unset($result['password']);
                    unset($result['last_activity']);
                    unset($result['status']);
                    Jsons::jsonOutput(true, $result);
                }else{
                    Jsons::jsonOutput(false, 'login', 'not found');
                }
            }else{
                Jsons::jsonOutput(false, 'login', 'empty');
            }
        }else{
            Jsons::jsonOutput(false, 'login', 'unauth');
        }
    }

    function getContacts()
    {
    }

    function getLastActivity()
    {

    }
}