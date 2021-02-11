<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset: utf-8");
set_include_path(get_include_path().'/');
spl_autoload_extensions('.php');
spl_autoload_register();
Router::start();
/*echo $hash = "$2y$10$9Clcl8U4Thn2eO1liHyFieM043tyh/sIlOOXxrrJ/dlirlbl04/x2";
if (password_verify('test', $hash)) {
    echo 'Пароль правильный!';
} else {
    echo 'Пароль неправильный.';
}*/


//Jsons::jsonOutput(true, "test1, test2, test3","value1, value2, value 3");
?>

