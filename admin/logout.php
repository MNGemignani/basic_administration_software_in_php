<?php
session_start();
require_once 'User.php';
$user_home = new User();

//check if user is loged in
if(!$user_home->is_logged_in())
{
    $user_home->redirect('../index.php');
}

if($user_home->is_logged_in()!="")
{
    $user_home->logout();
    $user_home->redirect('../index.php');
}
