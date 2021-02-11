<?php
class DBconnect
{
    public function connect()
    {
        $mysqli = @new mysqli('localhost', 'root', 'root', 'umessenger');
        if(mysqli_connect_errno()){
            echo 'Ошибка'.mysqli_connect_errno();
        }
        return $mysqli;
    }
}