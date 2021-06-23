<?php

class Get extends DBconnect
{
    function getContent($url_data)
    {     
        try{
            $this->$url_data();
        }
        catch(Throwable $url_data){
            
            Jsons::jsonOutput(false, 'method', 'unknown method called');
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
                unset($result['login']);
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
    function getDialogs($silent = null)
    {
        
        if($result = Gets::checkToken()){
            $uid = $result['uid'];
            $query = mysqli_query($this->connect(), "SELECT * FROM `dialog` WHERE `one_user_id` = '$uid' OR `two_user_id` = '$uid'");
            while($row=mysqli_fetch_assoc($query)){
                if($row['one_user_id'] == $uid){
                    unset($row['one_user_id']);
                    $id = $row['two_user_id'];
                    $qry = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `id` = '$id' ");
                    $tmp = mysqli_fetch_assoc($qry);
                    $row['secnod_user'] = $id;
                    $row['photo'] = $tmp['photo'];
                    $row['nick'] = $tmp['nick'];
                    unset($row['two_user_id']);
                }else{
                    unset($row['two_user_id']);
                    $id = $row['one_user_id'];
                    $qry = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `id` = '$id' ");
                    $tmp = mysqli_fetch_assoc($qry);
                    $row['secnod_user'] = $id;
                    $row['photo'] = $tmp['photo'];
                    $row['nick'] = $tmp['nick'];
                    unset($row['one_user_id']);
                }
                $data[] = $row;
            }
            if($silent == true){
                return ($data);
            }else{
                Jsons::jsonOutput(true, $data);
            }
        }else{
            Jsons::jsonOutput(false, 'login','unauth');
        }
    }

    function getMessageOld($dialog = '', $silent = null)
    {

        if(Gets::checkToken()){
           
           // var_dump($cnl);
            $url = $_SERVER['REQUEST_URI'];
            if($dialog != ''){
                $dialog_id = $dialog;
                
            }else{
                $dialog_id = explode('/', $url)[2];
            }
            $date = date("2021-02-06");
            if(!empty($dialog_id)){
                $query = mysqli_query($this->connect(), "SELECT * FROM `messages` WHERE `dialog_id` = '$dialog_id'");
                if($query == true){
                    while($row=mysqli_fetch_assoc($query)){                    
                       
                            $data[] = $row;
                        
                    
                    }
                   
                   
                    //var_dump($finel_arr);
                    
                  
                    if(empty($data)){
                        if($silent == null){
                            Jsons::jsonOutput(true, "noti", "no mess");
                        }
                    }else{
                        if($silent == true){
                            return ($data);
                        }else{
                            Jsons::jsonOutput(true, $data);

                        }

                    }
                    
                   /* if(!empty($data)){
                    }else{
                        Jsons::jsonOutput(false, "dialog", "access denied");
                    }*/
                }else{
                    Jsons::jsonOutput(false, "message", "some problem");
                }
            }else{
                Jsons::jsonOutput(false, 'dialog_id', 'not exist');
            }
            
        }else{
            Jsons::jsonOutput(false, "auth", "error");
        }
    }



    function getMessage($dialog = '', $silent = null)
    {

        if(Gets::checkToken()){
            $cnl = $this->getsub(true);
           // var_dump($cnl);
            $url = $_SERVER['REQUEST_URI'];
            if($dialog != ''){
                $dialog_id = $dialog;
                
            }else{
                $dialog_id = explode('/', $url)[2];
            }
            $date = date("2021-02-06");
            if(!empty($dialog_id)){
                $query = mysqli_query($this->connect(), "SELECT * FROM `messages` WHERE `dialog_id` = '$dialog_id'");
                if($query == true){
                    while($row=mysqli_fetch_assoc($query)){                    
                        if($row['message_file'] == ''){
                        unset($row['message_file']);
                        unset($row['type']);
                        $data[] = $row;
                        }else{
                            $data[] = $row;
                        }
                    
                    }
                   
                    $len = count($cnl);
                    for ($i=0; $i < $len; $i++) { 
                      //  print_r($cnl[$i]);
                        $data_sub[] =($cnl[$i]); 
                    }
                    $finel_arr = array_merge($data, $cnl);
                   // var_dump($finel_arr);
                    
                  
                    if(empty($data)){
                        if($silent == null){
                            Jsons::jsonOutput(true, "noti", "no mess");
                        }
                    }else{
                        if($silent == true){
                            return ($data);
                        }else{
                            Jsons::jsonOutput(true, $finel_arr);

                        }

                    }
                    
                   /* if(!empty($data)){
                    }else{
                        Jsons::jsonOutput(false, "dialog", "access denied");
                    }*/
                }else{
                    Jsons::jsonOutput(false, "message", "some problem");
                }
            }else{
                Jsons::jsonOutput(false, 'dialog_id', 'not exist');
            }
            
        }else{
            Jsons::jsonOutput(false, "auth", "error");
        }
    }

    function getMessChann()
    {
        if(Gets::checkToken()){
          
          
            $url = $_SERVER['REQUEST_URI'];
            
                $chanell_id = explode('/', $url)[2];
                
                if(!empty($chanell_id)){
                    $query = mysqli_query($this->connect(), "SELECT * FROM `channell_messages` WHERE `id_channel` = '$chanell_id'");
                    if($query == true){
                        while($row = mysqli_fetch_assoc($query)){
                            $data[] = $row;
                        }

                        Jsons::jsonOutput(true, $data);
                    }
                }
        }
    }

	/**
	 * поиск по логину
	 * search/login
	 *
	 */
    function search($silent = null)
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

                    if($silent == true){
                        return ($result);
                    }else{
                        Jsons::jsonOutput(true, $result);
                    }
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

	/**
	 * провекра авторизации
	 * хочет токен
	 *
	 */
    function checkAuth()
    {
        if(Gets::checkToken()){
            Jsons::jsonOutput(true, 'auth', 'ok');
        }else{
            Jsons::jsonOutput(false, 'auth', 'unauth');
        }
    }

    function userSearch()
    {

        if(Gets::checkToken()){
            $url = $_SERVER['REQUEST_URI'];
            $name = explode('/', $url)[2];
            $query = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `nick` LIKE '%$name%'");
            $i = 0;
            if(mysqli_num_rows($query) > 0 ){
                while($row = mysqli_fetch_assoc($query)){
                    $data[] = $row;
                    unset($data[$i]['password']);
                    unset($data[$i]['last_activity']);
                    unset($data[$i]['status']);
                    $i++;
                }
				Jsons::jsonOutput(true, $data);
			}else{
				Jsons::jsonOutput(false, 'data', 'empty');

			}

		}
    }

	/**
	 * поиск канала
	 * chanellSearch/имя канала
	 *
	 */
    function chanellSearch()
    {
        if($res = Gets::checkToken()){
            $url = $_SERVER['REQUEST_URI'];
            $name = explode('/', $url)[2];
            $query = mysqli_query($this->connect(), "SELECT * FROM `channels` WHERE `channel_name` LIKE '%$name%'");
           if(mysqli_num_rows($query)>0){
                while($row = mysqli_fetch_assoc($query)){
                   $data[] = $row;
                }
                Jsons::jsonOutput(true, $data);
            }else{
                Jsons::jsonOutput(false, 'err', 'not_found');
            }

        }


    }

    function getAllFiles()
    {
        if($res = Gets::checkToken()){
            $id = $res['id'];
            $query = mysqli_query($this->connect(), "SELECT * FROM `files` WHERE `owner_id` = '$id'");
            if(mysqli_num_rows($query) != 0){
                while($row = mysqli_fetch_assoc($query)){
                    $data[] =$row; 
                }
                Jsons::jsonOutput(true, $data);
            }else{
                Jsons::jsonOutput(false, 'data', 'no files');
            }
            
        }
    }

    function getsub($silent = null)
    {
        if($res = Gets::checkToken()){
            $id = $res['id'];
            $query = mysqli_query($this->connect(), "SELECT * FROM `chanell_subs` WHERE `uid` = '$id'");
            if(mysqli_num_rows($query) != 0){
                while($row = mysqli_fetch_assoc($query)){
                    $data[] =$row; 
                }
                
                if($silent!=true){
                    Jsons::jsonOutput(true, $data);
                }else{
                    return $data;

                }
            }else{
                Jsons::jsonOutput(false, 'data', 'no subs');
            }
            
        }
    }

}