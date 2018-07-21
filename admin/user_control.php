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

//get user information
$stmt = $user_home->runQuery("SELECT * FROM tbl_users WHERE id=:uid");
$stmt->execute(array(":uid"=>$_SESSION['userSession']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

//signup from user
if(isset($_POST['btn-signup']))
{
    $permissions = '';
    $full_name = trim($_POST['full_name']);
    $full_name_pat = trim($_POST['full_name_pat']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $bday = trim($_POST['bday']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $zipcode = trim($_POST['zipcode']);
    $phone = trim($_POST['phone']);
    $sdate = trim($_POST['sdate']);
    $plan = $_POST['plan'];
    $permissions = $_POST['admin'];

    if(!empty($_POST['admin'])){
        $permissions = $_POST['admin'];
    }
    else{
        $permissions = '';
    }

    if($permissions == 'on'){
        $permissions = 1;
    }
    else{
        $permissions = 0;
    }
    if($full_name_pat == ''){
        $full_name_pat = 'not apliable';
    }

    $stmt = $user_home->runQuery("SELECT * FROM tbl_users WHERE email=:email_id");
    $stmt->execute(array(":email_id"=>$email));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    //check email in dtatabase
    if($stmt->rowCount() > 0) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  email allready exists , Please Try another one
			  </div>
			  ";
    }
    else if(empty($full_name)) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  We need a Name.
			  </div>
			  ";
    }
    else if(empty($email)) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  We need a email.
			  </div>
			  ";
    }
    else if(empty($password)) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  We need a password.
			  </div>
			  ";
    }
    else if(empty($bday)) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  We need the date of birth.
			  </div>
			  ";
    }
    else if(empty($address)) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  We need a adres.
			  </div>
			  ";
    }
    else if(empty($city)) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  We need a city.
			  </div>
			  ";
    }
    else if(empty($zipcode)) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  We need a zipcode.
			  </div>
			  ";
    }
    else if(empty($phone)) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  We need a phone.
			  </div>
			  ";
    }
    else if(empty($sdate)) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  We need a sigunp date.
			  </div>
			  ";
    }
    else if(empty($plan)) {
        $msg = "
		      <div class='alert alert-error'>
				<button class='close' data-dismiss='alert'>&times;</button>
					<strong>Sorry !</strong>  We need to select a plan.
			  </div>
			  ";
    }
    else
    {
        if($user_home->register($full_name,$full_name_pat,$email,$password,$bday,$address,$city,$zipcode,$phone,$sdate,$plan,$permissions))
        {

            $msg = "
					<div class='alert alert-success'>
						<button class='close' data-dismiss='alert'>&times;</button>
						<strong>Success!</strong>  The User is registreted 
			  		</div>
					";
        }
        else
        {
            echo "sorry , Query could no execute...";
        }
    }
}


include_once 'includes_admin/head_admin.php';
include_once 'includes_admin/nav_admin.php';
?>
    <br><br><br>
    <h1 class="text-center">Add User</h1>
    <br><br>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <?php if(isset($msg)) echo $msg;  ?>
            <form action="user_control.php" method="post">

                <!--FULL NAME-->
                <div class="form-group col-md-6">
                    <label for="full_name" class="col-2 col-form-label">Full Name*</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="" id="full_name" name="full_name">
                    </div>
                </div>

                <!--FULL NAME OUDERS-->
                <div class="form-group col-md-6">
                    <label for="full_name_pat" class="col-2 col-form-label">Full Name Parents</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="" id="full_name_pat" name="full_name_pat">
                    </div>
                </div>

                <!--EMAIL-->
                <div class="form-group col-md-6">
                    <label for="email" class="col-2 col-form-label">Email*</label>
                    <div class="col-10">
                        <input class="form-control" type="email" value="" id="email" name="email">
                    </div>
                </div>

                <!--PASSWORD-->
                <div class="form-group col-md-6">
                    <label for="password" class="col-2 col-form-label">Password*</label>
                    <div class="col-10">
                        <input class="form-control" type="password" value="" id="password" name="password">
                    </div>
                </div>

                <!--DATE BIRTH-->
                <div class="form-group col-md-6">
                    <label for="bday" class="col-2 col-form-label">Birth Date*</label>
                    <div class="col-10">
                        <input class="form-control" type="date" value="" id="bday" name="bday">
                    </div>
                </div>

                <!--PHONE-->
                <div class="form-group col-md-6">
                    <label for="phone" class="col-2 col-form-label">Telephone*</label>
                    <div class="col-10">
                        <input class="form-control" type="tel" value="" id="phone" name="phone">
                    </div>
                </div>

                <!--ADDRESS-->
                <div class="form-group col-md-12">
                    <label for="address" class="col-2 col-form-label">Address*</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="" id="address" name="address">
                    </div>
                </div>

                <!--CITY-->
                <div class="form-group col-md-6">
                    <label for="city" class="col-2 col-form-label">City*</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="" id="city" name="city">
                    </div>
                </div>

                <!--ZIPCODE-->
                <div class="form-group col-md-6">
                    <label for="zipcode" class="col-2 col-form-label">Zipcode*</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="" id="zipcode" name="zipcode">
                    </div>
                </div>

                <!--SIGN UP DATE-->
                <div class="form-group col-md-6">
                    <label for="sdate" class="col-2 col-form-label">Sigunp Date*</label>
                    <div class="col-10">
                        <input class="form-control" type="date" value="" id="sdate" name="sdate">
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <label for="plan">Abonamment*</label>
                    <select multiple class="form-control" id="plan" name="plan">
                        <option value=""></option>
                        <?php
                        //get all plans
                        $stmt_plan = $user_home->runQuery("SELECT * FROM plan");
                        $stmt_plan->execute();

                        while($row_plan = $stmt_plan->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $row_plan['id'];?>"><?php echo $row_plan['type'];?></option>
                        <?php endwhile;?>
                    </select>
                </div>

                <!--PERMISSIONS-->
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" value="admin" name="admin">
                        Administrator
                    </label>
                </div>

                <!--SUBMIT BUTTON-->
                <button class="btn btn-large btn-primary" type="submit" name="btn-signup">Add User</button>
            </form>
        </div><!--EIND DIV COL-MD-8-->
        <div class="col-md-2"></div>
    </div>
<?php

include_once 'includes_admin/footer_admin.php';