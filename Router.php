<?php

class Router
{
    static function start()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $_SERVER['REQUEST_URI'];
        $url_data = explode('/', $url); //str_replace
        //заменить на if
        switch($method)
        {
            case "POST":
                $post = new Post;
                $post->getContent($url_data[1]);
            break;

            case "GET":
                $get = new Get;
                $get->getContent($url_data[1]);
            break;
        }
    }
}