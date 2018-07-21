<?php
session_start();

//require the classes
require_once 'User.php';

//create the instances
$user_home = new User();

//check if user is loged in
if(!$user_home->is_logged_in())
{
    $user_home->redirect('../index.php');
}


if(isset($_GET['msg'])){
    $msg = $_GET['msg'];
    if($msg == 'mail_success'){
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Email Sent</strong> 
			  </div>
			  ";
    }
}

include_once 'includes_admin/head_admin.php';
include_once 'includes_admin/nav_admin.php';
?>
<br><br><br>
<h3>Hello, Administrator <?php echo $row['full_name']; ?></h3>

   
   <br><br><br><br><br><br><br><br><br><br><br><br>
<?php

include_once 'includes_admin/footer_admin.php';