<?php
//get admin information
$stmt_navbar = $user_home->runQuery("SELECT * FROM tbl_users WHERE id=:uid");
$stmt_navbar->execute(array(":uid"=>$_SESSION['userSession']));
$row = $stmt_navbar->fetch(PDO::FETCH_ASSOC);
?>
<!--Navbar-->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php" style="margin-bottom: 5px;">
                <img src="../img/minilogo.png" width="35" height="35" alt="">
            </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="index.php">Home <span class="sr-only">(current)</span></a></li>
                <li><a href="user_control.php">Add User</a></li>
                <li><a href="user_table.php">User Control</a></li>
                <li><a href="payments.php">Create Invoice</a></li>
                <li><a href="facturen.php">Invoice Control</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown ">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-user"></i><?php echo $row['email']; ?> <i class="caret"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a tabindex="-1" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>