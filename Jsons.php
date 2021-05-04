<?php

class Jsons
{
    /**
     * @param bool $status статус
     * @param string $name_key имя ключа
     * @param string $value значения ключей (необязательный параметр)
     * @return json
     */
    static function jsonOutput($status, $name_key, $value = null)
    {
        if(is_array($name_key) == true){
            if($status == true){
                echo json_encode([
                    "status"=>$status,
                    "payload"=>$name_key
                ]);
            }else{
                echo json_encode([
                    "status"=>$status,
                    "error"=>$name_key
                ]);
            }
        }else{
            if($status == true){
                echo json_encode([
                    "status"=>$status,
                    "payload"=>array_combine(explode(',',str_replace(' ', '',$name_key)) , explode(',', $value))
                ]);
            }else{
                echo json_encode([
                    "status"=>$status,
                    "error"=>array_combine(explode(',',str_replace(' ', '',$name_key)) , explode(',', $value))
                ]);
            }
        }
    }
}