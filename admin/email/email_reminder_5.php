<?php
session_start();

//require the classes
require_once '../User.php';

//create the instances
$user_home = new User();

//check if user is loged in
if(!$user_home->is_logged_in()){
    $user_home->redirect('../index.php');
}

$factuur_name = htmlentities($_GET['factuur'], ENT_QUOTES, "UTF-8");
$email = htmlentities($_GET['email'], ENT_QUOTES, "UTF-8");

//get information from payment and user
$stmt_fac = $user_home->runQuery("SELECT * FROM factuur WHERE factuur_name=:fname");
$stmt_fac->execute(array(":fname"=>$factuur_name));
$row_fac = $stmt_fac->fetch(PDO::FETCH_ASSOC);

$factuur_total = $row_fac['factuur_totaal'];
$factuur_number = $row_fac['factuur_nummer'];

//get user information
$stmt_user = $user_home->runQuery("SELECT * FROM tbl_users WHERE email=:email");
$stmt_user->execute(array(":email"=>$email));
$row_user = $stmt_user->fetch(PDO::FETCH_ASSOC);
$user_name = $row_user['full_name'];
$user_plan = $row_user['plan'];

if ($user_plan == 7 || $user_plan== 8 || $user_plan== 9 || $user_plan== 10){
    $subject = 'Herinnering nota van Barao Jiu Jitsu met factuurnummer: '.$factuur_number;
}
else{
    $subject = 'Herinnering nota van Barao Jiu Jitsu Amstelveen met factuurnummer: '.$factuur_number;
}

$message = '
                <img src="http://www.baraojiujitsu.com/img/download.png"><br>
                <p>Geachte heer/mevrouw '.$user_name.',</p><br>
                <p>Dit is een vriendelijk herinnering dat u nota binnen 5 dagen moet voldoen worden. </p><br>
                <p>Namens Barao Jiu Jitsu Nederland heeft u een nota ontvangen met nummer: '.$factuur_number.'.</p><br>
                <p>Met Vriendelijke groeten,</p><br>
                <p>Barão Jiu Jitsu Amstelveen</p><br><br>
                
                <p>Als je al hebt betaald, negeer dit bericht.</p>
                ';
$atach = '../factuur_files/'.$factuur_name.'.pdf';

$user_home->send_mail($email,$message,$subject,$atach);

header('Location: ../index.php?msg=mail_success');