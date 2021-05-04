<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-type: application/json; charset: utf-8");
set_include_path(get_include_path().'/');
spl_autoload_extensions('.php');
spl_autoload_register();
Router::start();
?>

