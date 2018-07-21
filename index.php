<?php
session_start();

require_once 'admin/User.php';
$user_login = new User();

if($user_login->is_logged_in()!="")
{
    $user_login->redirect('admin/index.php');
}

if(isset($_POST['btn-login']))
{
    $email = trim($_POST['txtemail']);
    $upass = trim($_POST['txtupass']);

    if($user_login->login($email,$upass)) {
        //get the user permission
        $user_id = $_SESSION['userSession'];
        $stmt = $user_login->runQuery("SELECT * FROM tbl_users WHERE id=:user_id");
        $stmt->execute(array(":user_id"=>$user_id));
        $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
        if($userRow['permissions'] == 1) {
            $user_login->redirect('admin/index.php');
        }
        else {
            $user_login->redirect('admin/index.php');
        }
    }
}

?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Login</title>
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
        <link href="admin/assets/styles.css" rel="stylesheet" media="screen">

    </head>
    <body id="login">
    <div class="container">

        <form class="form-signin" method="post">
            <?php
            if(isset($_GET['error']))
            {
                ?>
                <div class='alert alert-success'>
                    <button class='close' data-dismiss='alert'>&times;</button>
                    <strong>Wrong Details!</strong>
                </div>
                <?php
            }
            ?>
            <h2 class="form-signin-heading">Sign In</h2><hr />
            <input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
            <input type="password" class="input-block-level" placeholder="Password" name="txtupass" required />
            <hr />
            <button class="btn btn-large btn-primary" type="submit" name="btn-login">Sign in</button>
        </form>

    </div> <!-- /container -->
    <script src="bootstrap/js/jquery-1.9.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    </body>
    </html>

