<?php
class Post extends DBconnect
{
    function getContent($url_data)
    {
		//$this->deleteMess();
        //$this->setChanellPhoto();
		try{
			$this->$url_data();
		}
        catch(Throwable $url_data){
            Jsons::jsonOutput(false, 'method', 'unknown method called');
        }
    }

    function login()
    {
        $login = $_POST['login'];
        $pass = $_POST['password'];
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
                    $token = hash("sha256", uniqid('', true));
                    $result['token'] = $token;
                    $query = mysqli_query($this->connect(), "INSERT INTO `token`(`uid`, `token`) VALUES ('$id', '$token')");
                    if($query == true){
                        $date = date("Y-m-d H:i:s");
                        mysqli_query($this->connect(), "UPDATE `users` SET `status`= 'online', `last_activity` = '$date' WHERE `id` = '$id'");
                        unset($result['password']);
                        unset($result['last_activity']);
                        unset($result['status']);
                        Jsons::jsonOutput(true, $result);
                   }else{
                        Jsons::jsonOutput(false, "unauth", "prikol");
                   }
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
                VALUES ('$login','$nick','$pass','files/images/standart.jpg','offline','user')");
                if($query == true){
                    Jsons::jsonOutput(true ,"login", "register");
                }else{
                    Jsons::jsonOutput(false,"sql", "sql error");
                }
            }else{
                Jsons::jsonOutput(false,"login", "login exist");
            }
        }else{
            Jsons::jsonOutput(false,"login", "data empty");
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
        if($result = Gets::checkToken()){
            $uid = $result['uid'];
            $type_file = explode('.',$_FILES['userfile']['name']);
            if(end($type_file) == 'jpg' or end($type_file) == 'jpeg' or end($type_file) == 'png')
            {
                $file = $this->uploadFile($_FILES,true);
                $path = $file['path'];
                mysqli_query($this->connect(), "UPDATE `users` SET `photo` = '$path' WHERE `id` = '$uid'");
                $array["name"]=$file['name'];
                $array["path"]=$file['path'];
                Jsons::jsonOutput(true, $array);
            }else{
                Jsons::jsonOutput(false, "err", 'not image');
            }
        }else{
            Jsons::jsonOutput(false,"auth", 'unauth');
        }

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
            $date = date("Y-m-d H:i:s");
                mysqli_query($this->connect(),"UPDATE `token` SET `token` = 'logout' WHERE `uid` = '$id' AND `token` = '$token'");
                mysqli_query($this->connect(), "UPDATE `users` SET `status`= 'offline',`last_activity`= '$date' WHERE `id` = '$id'");
                Jsons::jsonOutput(true, 'login', 'logout');
            
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
     * загрузка файлов
	 * userfile - файл
	 * type - тип файла (не обязательно)
     *
     */

    function uploadFile($upfile = null, $silent = null, $type = null)
    {
        if($result = Gets::checkToken()){
            $uid = $result['uid'];
            $type_file = explode('.',$_FILES['userfile']['name']);
            if(end($type_file) == 'jpg' or end($type_file) == 'jpeg' or end($type_file) == 'png')
            {
                $uploaddir = 'files/images/';
                if($type == null){
                    $type = 'image';

                }
            }else{
                if(end($type_file) == 'mp4')
                {
                    $uploaddir = 'files/video/';
                    if($type == null){
                    $type = 'video';
                    }
                }
                if(end($type_file) == 'mp3')
                {
                    $uploaddir = 'files/sounds/';
                    if($type == null){
                    $type = 'music';
                    }
                }else{
                   
                    $uploaddir = 'files/other/';
                    if($type == null){
                    $type = 'file';
                    }
                }
            }
          

                $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
                $sha = hash_file("sha256", $_FILES['userfile']['tmp_name']);

            $file =md5( base64_encode(microtime(true))).'.'.explode('.', $uploadfile)[1];
        
            $query = mysqli_query ($this->connect(), "SELECT * FROM `files` WHERE `hash_sum` = '$sha'");
            if(mysqli_num_rows($query) == 1){
                $data = mysqli_fetch_assoc($query);
                $arr['name'] = $data['name']; 
                $arr['path'] = $data['path']; 
                $arr['time'] = $data['time_upload']; 
                $arr['type'] = $data['type']; 
                if($silent == true){
                    return ($arr);
                }else{
                    Jsons::jsonOutput(true, $arr);
                }
            }else{
                if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir.$file)) {
                    $path = $uploaddir.$file;
                    $name = $_FILES['userfile']['name'];
                    $date = date("Y-m-d H:i:s");
                    
                    mysqli_query($this->connect(),"INSERT INTO `files`(`name`, `path`, `owner_id`, `hash_sum`, `time_upload`, `type`)
                    VALUES ('$name','$path','$uid','$sha', '$date', '$type')");
                    $array["name"]=$name;
                    $array["path"]=$path;
                    $array["type"]=$type;
                    if($silent == true){
                        return ($array);
                    }else{
                        Jsons::jsonOutput(true, $array);
                    }
                } else {
                    if($silent != true){
                        Jsons::jsonOutput(false,'photo', 'some error push form-data to redgroul');
                       
                    }
                   
                }
            }
           

    
          
        }
    }

    function testupl()
    {
       $res = $this->uploadFile($_FILES['userfile']['name'], true, 'soundmess');
       var_dump($res);
    }

	/**
	 * репорт
	 * report_user_id - id на кого репорт
	 * message - сообщение
	 * type - тип :D
	 *
	 */

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

	/**
	 * Отправка сообщений
	 * sendMessage/dialog_id/second_user_id
	 * message - сообщение
	 * message_file - файл для сообщения
	 * type - тип файла (не обязательно)
	 */

    function sendMessage()
    {
        $url = $_SERVER['REQUEST_URI'];
        $dialog_id = explode('/', $url)[2];
        $get_user_id = explode('/', $url)[3];

        $type = $_POST['type'];
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
                    if(!empty($message) or !empty($_FILES)){
                        if(!empty($_FILES)){
                         $file = @$this->uploadFile($_FILES,true, $type);
                         
                         $uploadedfile = $file['path'];
                        // echo $uploadedfile;
                         $type = trim($file['type']);
                        }
                       $query = mysqli_query($this->connect(), "INSERT INTO `messages`(`dialog_id`, `message_text`,`message_file`,`type`, `sender`, `getter`, `data_send`, `is_read`, `visible_one`, `visible_two`)
                        VALUES ('$dialog_id','$message', '$uploadedfile','$type', '$id', '$get_user_id','$date','0','1','1')");
                        if($query == true){
                             
                            Jsons::jsonOutput(true, "message, get_user, date_send, file, type_file", "'$message','$get_user_id','$date', '$uploadedfile',$type");
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

    function createDialog()
    {
        if($result = Gets::checkToken()){
            $get_user_id = $_POST['user_id'];
            $id = $result['id'];
            if(!empty($get_user_id)){
                $query = mysqli_query($this->connect(), "SELECT * FROM `users` WHERE `id` = '$get_user_id'");
                if(mysqli_num_rows($query) == 1){
                    $date = date("Y-m-d H:i:s");
                    $check_dialog = mysqli_query($this->connect(), "SELECT * FROM `dialog` WHERE `one_user_id` = '$id' AND `two_user_id` = '$get_user_id' or `two_user_id` = '$id' AND `one_user_id` = '$get_user_id'");
                    if(mysqli_num_rows($check_dialog)>0){
                        $data = mysqli_fetch_assoc($check_dialog);
                        Jsons::jsonOutput(false, $data);
                    }else{
                        $query = mysqli_query($this->connect(), "INSERT INTO `dialog`(`one_user_id`, `two_user_id`, `time_create`)
                        VALUES ('$id','$get_user_id','$date')");
                        if($query == true){
                            Jsons::jsonOutput(true, "dialog", "created");
                        }
                    }

                }else{
                    Jsons::jsonOutput(false, "dialog", "user not found");
                }
            }else{
                Jsons::jsonOutput(false, "error", "error");
            }
        }else{
            Jsons::jsonOutput(false, "auth", "uauth");
        }



    }

	/**
	 *
	 * Создание канала
	 * cname - имя
	 * description - описание
	 * userfile - картинка
	 *
	 */
    function createChannel()
    {
        if($result = Gets::checkToken())
        {
            $creator = $result['id'];
            $channel_name = $_POST['cname'];
            
            $description = $_POST['description'];
            $access = $_POST['access'];
            $link = 'unmess.com/'.$channel_name;
            if(!empty($channel_name)){
            $check_name = mysqli_query($this->connect(), "SELECT `channel_name` FROM `channels` where `channel_name` = '$channel_name'");
                if(mysqli_num_rows($check_name) == 0){
                    if(!empty($_FILES)){
                        $type_file = explode('.',$_FILES['userfile']['name']);
                        if(end($type_file) == 'jpg' or end($type_file) == 'jpeg' or end($type_file) == 'png')
                        {
                         $file = $this->uploadFile($_FILES,true, $type);
                         $uploadedfile = $file['path'];
                        }
                        
                    }
                    $add_channel = mysqli_query($this->connect(), "INSERT INTO `channels`(`channel_name`, `photo`, `description`, `access`, `users_count`, `creator`, `content_managers`, `link`) 
                        VALUES ('$channel_name', '$uploadedfile', '$description', '$access', '1', '$creator', '', '$link')");
                    if($add_channel == true){

                        $get_chanell_id = mysqli_query($this->connect(), "SELECT * FROM `channels` WHERE `channel_name` = '$channel_name' AND `creator` = '$creator' AND `link` = '$link'");
                        $id = mysqli_fetch_assoc($get_chanell_id)['id'];
                        $array['id'] = $id;
                        $array['channel_name'] = $channel_name;
                        $array['creator'] = $creator;
                        $array['link'] = $link;
                        mysqli_query($this->connect(), "INSERT INTO `chanell_subs`(`uid`, `channel_name`, `channel_id`) VALUES ('$creator', '$channel_name', '$id')");
                        Jsons::jsonOutput(true, $array);
                    }else{
                        Jsons::jsonOutput(false, 'error', 'create error');
                    }

                }else{
                    Jsons::jsonOutput(false, 'error', 'channel with name: '.$channel_name.' exist!');
                }
            }else{
                Jsons::jsonOutput(false, 'error', 'no chanell name');

            }
        }else{
            Jsons::jsonOutput(false, 'error', 'auth');

        }
    }

	/**
	 * Изменение картинка канала
	 *	/setChanellPhoto/id_канала
	 * userfile - картинка
	 *
	 */
    function setChanellPhoto()
    {
        if($res = Gets::checkToken()){
            $uid = $res['id'];
            $url = $_SERVER['REQUEST_URI'];
            $chanell_id = explode('/', $url)[2];
            $type_file = explode('.',$_FILES['userfile']['name']);
            if(end($type_file) == 'jpg' or end($type_file) == 'jpeg' or end($type_file) == 'png')
            {
                $file = $this->uploadFile($_FILES,true);
                $path = $file['path'];
               $query = mysqli_query($this->connect(), "UPDATE `channels` SET `photo` = '$path' WHERE `id` = '$chanell_id' AND `creator` = '$uid'");
               if($query == true){
                   $array["name"]=$file['name'];
                   $array["path"]=$file['path'];
                   Jsons::jsonOutput(true, $array);
               }else{
                   Jsons::jsonOutput(false, "err", 'some error');

               }
            }else{
                Jsons::jsonOutput(false, "err", 'not image');
            }

        }
    }

	/**
	 *
	 * изменение данных канала
	 * 	changeDataChannel/id канала
	 * 	cname- имя
	 * description - описание
	 *
	 */
    function changeDataChannel()
    {
        if($res = Gets::checkToken())
        {
            $uid = $res['id'];
            $url = $_SERVER['REQUEST_URI'];
            $chanell_id = explode('/', $url)[2];

            if(!empty($chanell_id)){
                $cname = $_POST['cname'];
                $desc = $_POST['description'];
                $query = mysqli_query($this->connect(), "UPDATE `channels` SET `channel_name` = '$cname', `description`='$desc' WHERE `id` = '$chanell_id'");
                if($query == true){
                    Jsons::jsonOutput(true, 'result', 'data update');

                }else{
                    Jsons::jsonOutput(false, 'err', 'sql err');
                }
            }else{
                Jsons::jsonOutput(false, 'data', 'data id empty');
            }
            
        }
    }
    /**
    * messageChannel/id_c
    *message - имя
    *userfile - файл
    * type - тип
    */
    function messageChannel()
    {
        if($res = Gets::checkToken()){
            $uid = $res['id'];
            $url = $_SERVER['REQUEST_URI'];
            $type = $_POST['type'];
            $chanell_id = explode('/', $url)[2];
            $query1 = mysqli_query($this->connect(), "SELECT * FROM `channels` WHERE `creator` = '$uid' AND `id` = '$chanell_id'");
            if(mysqli_num_rows($query1) > 0){
                $message = $_POST['message'];
                if(!empty($_FILES)){
                    $file = $this->uploadFile($_FILES,true , $type);
                    $path = $file['path'];

                }
                $name_f = $file['name'];
                $type_f = $file['type'];
                $query = mysqli_query($this->connect(), "INSERT INTO `channell_messages`(`id_channel`, `message`, `file`, `file_name`, `file_type`) 
                VALUES ('$chanell_id','$message','$path','$name_f','$type_f')");
                if($query == true){
                    Jsons::jsonOutput(true, "data", 'ok');
                }else{
                    echo $chanell_id;
                    echo $message;
                    echo $path;
                    echo $type_f;
                    Jsons::jsonOutput(true, "data", 'err');
                }
            }else{
                Jsons::jsonOutput(false, "err", "chanell not found or you don't have premissions");
            }
        }
    }
    /**
    * подписка
    * sub/id_chan;
    *
    *
    */
    function sub(){
        if($res = Gets::checkToken()){
            $uid = $res['id'];
            $url = $_SERVER['REQUEST_URI'];
            $type = $_POST['type'];
            $chanell_id = explode('/', $url)[2];

            $query1 = mysqli_query($this->connect(), "SELECT * FROM `channels` WHERE `id` = '$chanell_id'");
            $tmp = mysqli_fetch_assoc($query1);
            $nc = $tmp['channel_name'];
            if(mysqli_num_rows($query1)>0){
                $query2 = mysqli_query($this->connect(), "SELECT * FROM `chanell_subs` WHERE `uid` = '$uid' AND `channel_id` = '$chanell_id'");
                if(mysqli_num_rows($query2) == 0){
                   $qur = mysqli_query($this->connect(), "INSERT INTO `chanell_subs`( `uid`, `channel_name`, `channel_id`) VALUES ('$uid', '$nc', '$chanell_id')");
                    if($qur == true){
                        Jsons::jsonOutput(true, "sub", 'ok');
                    }else{
                        echo $chanell_id;
                    }
                }else{
                    Jsons::jsonOutput(false, "sub", 'no need');
                }
            }else{
                Jsons::jsonOutput(false, "sub", 'not found');
            }
        }
    }

    function deleteAllMess()
	{
		if($res = Gets::checkToken()){
			$uid = $res['id'];
			$url = $_SERVER['REQUEST_URI'];
			$dialog_id = explode('/', $url)[2];
			if(!empty($dialog_id)){
				$query = mysqli_query($this->connect(), "SELECT * FROM `dialog` WHERE `dialog_id` = '$dialog_id'");
				if(mysqli_num_rows($query) == 1){
					$del = mysqli_query($this->connect(), "DELETE FROM `messages` WHERE `dialog_id` = '$dialog_id'");
					if($del == true){
						Jsons::jsonOutput(true, 'dialog', 'delete');
					}
				}else{
					Jsons::jsonOutput(false, 'dialog','not found or no mess');
				}
			}
		}
	}



    function createConvers()
	{
		if(Gets::checkToken())
		{
			$name = $_POST['cname'];
			$description = $_POST['description'];

			if(!empty($name)){
				if(!empty($_FILES)){
					$file = $this->uploadFile($_FILES,true);
					$path = $file['path'];
				}
				$qur = mysqli_query($this->connect(), "INSERT INTO ` conversation_dialogs`(`name`, `description`, `count_users`, `photo`)
										VALUES ('$name','$description','1','$path')");
				if($qur == true){
					Jsons::jsonOutput(true, 'create convers', 'create');
				}
			}


		}
	}



	function deleteMess()
	{
		if($res = Gets::checkToken()){
			$uid = $res['id'];
			$url = $_SERVER['REQUEST_URI'];
		//	$dialog_id = explode('/', $url)[2];
			$mess_id = explode('/', $url)[2];

			$found = mysqli_query($this->connect(), "SELECT * FROM `messages` WHERE  `message_id` = '$mess_id'");
			if(mysqli_num_rows($found) == 1){
				$del = mysqli_query($this->connect(), "DELETE FROM `messages` WHERE  `message_id` = '$mess_id'");
				if($del == true){
					Jsons::jsonOutput(true, 'mess', 'delete');
				}
			}else{
				Jsons::jsonOutput(false, 'mess', 'not found');
			}
		}
	}

	function editMess()
	{
		if($res = Gets::checkToken()){
			print_r($uid = $res['id']);
			$url = $_SERVER['REQUEST_URI'];
		//	$dialog_id = explode('/', $url)[2];
			$mess_id = explode('/', $url)[2];
			$new_mess = $_POST['message'];

			if(!empty($new_mess)){
				$found = mysqli_query($this->connect(), "SELECT * FROM `messages` WHERE  `message_id` = '$mess_id'");
				if(mysqli_num_rows($found) == 1){
					$del = mysqli_query($this->connect(), "UPDATE `messages` SET `message_text` = '$new_mess' WHERE `message_id` = '$mess_id'");
					if($del == true){
						Jsons::jsonOutput(true, 'mess', 'update');
					}else{
						Jsons::jsonOutput(true, 'mess', 'err');

                    }
				}else{
					Jsons::jsonOutput(false, 'mess', 'not found');
				}
			}

		}
	}
}
