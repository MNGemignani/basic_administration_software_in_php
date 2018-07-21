<?php
session_start();

//require the classes
require_once 'User.php';

//create the instances
$user_home = new User();

//check if user is loged in
if(!$user_home->is_logged_in()){
    $user_home->redirect('../index.php');
}

//delete user
if(isset($_GET['delete'])){
    $user_id_delete = $_GET['delete'];
    $stmt_delete = $user_home->runQuery("DELETE FROM tbl_users WHERE id=:uid");
    if($stmt_delete->execute(array(":uid" => $user_id_delete))){
        ?>
        <!--success message and redirection to the admin page-->
        <script>
            alert('Succes deleted');
            window.location.href='user_table.php';
        </script>

        <?php
    }
    else{
        ?>
        <!--error message and redirection to the admin page-->
        <script>
            alert('An error has ocurred trying to delete the data');
            window.location.href='user_table.php';
        </script>

        <?php
    }

}
//edit user with id from GET
if(isset($_GET['edit'])){
    $user_id = $_GET['edit'];
    $stmt_edit = $user_home->runQuery("SELECT * FROM tbl_users WHERE id=:uid");
    $stmt_edit->execute(array(":uid"=>$user_id));
    $row_edit = $stmt_edit->fetch(PDO::FETCH_ASSOC);

    //get the values to fill fields in the form edit
    $full_name = $row_edit['full_name'];
    $full_name_pat = $row_edit['full_name_pat'];
    $email = $row_edit['email'];
    $password = $row_edit['password'];
    $bday = $row_edit['bday'];
    $address = $row_edit['address'];
    $city = $row_edit['city'];
    $zipcode = $row_edit['zipcode'];
    $phone = $row_edit['phone'];
    $sdate = $row_edit['sdate'];
    $plan = $row_edit['plan'];
    $permissions = $row_edit['permissions'];

    if($full_name_pat == "not apliable"){
        $full_name_pat = '';
    }

    //if admin cliks to edit user, update in database
    if(isset($_POST['submit'])){
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
        $permissions = $_POST['permissions'];

        if($permissions == 'on'){
            $permissions = 1;
        }
        else{
            $permissions = 0;
        }
        if($full_name_pat == ''){
            $full_name_pat = 'not apliable';
        }

        //check if nothing is empty
        if(empty($full_name)) {
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
        else {

            if($user_home->edit_user($full_name,$full_name_pat,$email,$password,$bday,$address,$city,$zipcode,$phone,$sdate,$plan,$permissions,$user_id)) {

                $msg = "
					<div class='alert alert-success'>
						<button class='close' data-dismiss='alert'>&times;</button>
						<strong>Success!</strong>  The User Information is Updated. 
			  		</div>
					";
                header( "Location: user_table.php" );
            }
            else {
                echo "sorry , Query could no execute...";
            }
        }

    }

}



include_once 'includes_admin/head_admin.php';
include_once 'includes_admin/nav_admin.php';
?>
<br><br><br>
<h2 class="text-center">Users Information</h2>
<hr>

<?php if(isset($_GET['edit'])): ?>
    <!--Form-->
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <?php if(isset($msg)) echo $msg;  ?>
            <form class="form" action="user_table.php<?php echo '?edit='.$user_id; ?>" method="post">
                <legend>Edit User</legend>

                <!--FULL NAME-->
                <div class="form-group col-md-6">
                    <label for="full_name" class="col-2 col-form-label">Full Name*</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="<?php echo $full_name; ?>" id="full_name" name="full_name">
                    </div>
                </div>

                <!--FULL NAME OUDERS-->
                <div class="form-group col-md-6">
                    <label for="full_name_pat" class="col-2 col-form-label">Full Name Parents</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="<?php echo $full_name_pat; ?>" id="full_name_pat" name="full_name_pat">
                    </div>
                </div>

                <!--EMAIL-->
                <div class="form-group col-md-6">
                    <label for="email" class="col-2 col-form-label">Email*</label>
                    <div class="col-10">
                        <input class="form-control" type="email" value="<?php echo $email; ?>" id="email" name="email">
                    </div>
                </div>

                <!--PASSWORD-->
                <div class="form-group col-md-6">
                    <label for="password" class="col-2 col-form-label">Password*</label>
                    <div class="col-10">
                        <input class="form-control" type="password" value="password" id="password" name="password">
                    </div>
                </div>

                <!--DATE BIRTH-->
                <div class="form-group col-md-6">
                    <label for="bday" class="col-2 col-form-label">Birth Date*</label>
                    <div class="col-10">
                        <input class="form-control" type="date" value="<?php echo $bday; ?>" id="bday" name="bday">
                    </div>
                </div>

                <!--PHONE-->
                <div class="form-group col-md-6">
                    <label for="phone" class="col-2 col-form-label">Telephone*</label>
                    <div class="col-10">
                        <input class="form-control" type="tel" value="<?php echo $phone; ?>" id="phone" name="phone">
                    </div>
                </div>

                <!--ADDRESS-->
                <div class="form-group col-md-12">
                    <label for="address" class="col-2 col-form-label">Address*</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="<?php echo $address; ?>" id="address" name="address">
                    </div>
                </div>

                <!--CITY-->
                <div class="form-group col-md-6">
                    <label for="city" class="col-2 col-form-label">City*</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="<?php echo $city; ?>" id="city" name="city">
                    </div>
                </div>

                <!--ZIPCODE-->
                <div class="form-group col-md-6">
                    <label for="zipcode" class="col-2 col-form-label">Zipcode*</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="<?php echo $zipcode; ?>" id="zipcode" name="zipcode">
                    </div>
                </div>

                <!--SIGN UP DATE-->
                <div class="form-group col-md-6">
                    <label for="sdate" class="col-2 col-form-label">Sigunp Date*</label>
                    <div class="col-10">
                        <input class="form-control" type="date" value="<?php echo $sdate; ?>" id="sdate" name="sdate">
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <label for="plan">Abonamment*</label>
                    <select multiple class="form-control" id="plan" name="plan">
                        <option value=""></option>
                        <option value="1" <?php echo (($plan == "1")? "selected": "");?>>6 months single payment unlimited training</option>
                        <option value="2" <?php echo (($plan == "2")? "selected": "");?>>6 months montly payment unlimited training</option>
                        <option value="3" <?php echo (($plan == "3")? "selected": "");?>>Montly unlimited training</option>
                        <option value="4" <?php echo (($plan == "4")? "selected": "");?>>6 months montly payment 1xweek</option>
                        <option value="5" <?php echo (($plan == "5")? "selected": "");?>>6 months single payment 1xweek</option>
                        <option value="6" <?php echo (($plan == "6")? "selected": "");?>>Montly 1xweek</option>
                        <option value="7" <?php echo (($plan == "7")? "selected": "");?>>Personal Trainer 24 hours</option>
                        <option value="8" <?php echo (($plan == "8")? "selected": "");?>>Brazilian Jiu Jitsu Groeplessen 26 hours</option>
                        <option value="9" <?php echo (($plan == "9")? "selected": "");?>>Personal Trainer 12 hours</option>
                        <option value="10" <?php echo (($plan == "10")? "selected": "");?>>Personal Trainer 30 hours</option>
                        <option value="11" <?php echo (($plan == "11")? "selected": "");?>>Personal Trainer 8 hours</option>
                        <option value="12" <?php echo (($plan == "12")? "selected": "");?>>Montly 1xweek Kinderen</option>
                    </select>
                </div>

                <!--PERMISSIONS-->
                <div class="form-group">
                    <label for="permissions">Administrator</label>
                    <input type="checkbox" id="permissions" name="permissions">
                </div>
                <!--CANCEL BUTTONS WITH EDIT-->
                <a style="margin-right: 10px;" href="user_table.php" class="btn btn-default">Cancel</a>

                <!--SUBMIT BUTTON-->
                <div class="form-group">
                    <input type="submit" value="Edit User" class="btn btn-success" name="submit">
                </div>
            </form>
        </div><!--eind div col-md-6 from form-->
        <div class="col-md-3"></div>
    </div><!--eind row-->
<?php endif; ?>

<div class="row">
    <div class="col-md-1"></div>
    <!--TABLE WITH CONTENT INFORMATION-->
    <div class="col-md-10">
        <table id="employee_data" class="table table-bordered table-condensed table-striped">
            <thead>
                <th></th>
                <th>User id</th>
                <th>Name</th>
                <th>Name of Parents</th>
                <th>Email</th>
                <th>Address</th>
                <th>City</th>
                <th>Zipcode</th>
                <th>Phone</th>
                <th>Birth Date</th>
                <th>Sign up Date</th>
                <th>Abonnement</th>
            </thead>
            <tbody>
            <?php
            //get user information for table
            $stmt_table = $user_home->runQuery("SELECT * FROM tbl_users WHERE permissions!=1");
            $stmt_table->execute();
            while($row_table = $stmt_table->fetch(PDO::FETCH_ASSOC)):
                $user_id = (int)$row_table['id'];
                $user_name = $row_table['full_name'];
                $user_pat_name = $row_table['full_name_pat'];
                $user_email = $row_table['email'];
                $user_address = $row_table['address'];
                $user_city = $row_table['city'];
                $user_zipcode = $row_table['zipcode'];
                $user_phone = $row_table['phone'];
                $user_bday = $row_table['bday'];
                $user_sdate = $row_table['sdate'];
                $user_plan = $row_table['plan'];

                //change what it shows for the abonnement
                if($user_plan == "1"){
                    $user_plan = "6 months single payment unlimited training";
                }
                if($user_plan == "2"){
                    $user_plan = "6 months montly payment unlimited training";
                }
                if($user_plan == "3"){
                $user_plan = "Montly unlimited training";
                }
                if($user_plan == "4"){
                    $user_plan = "6 months montly payment 1xweek";
                }
                if($user_plan == "5"){
                    $user_plan = "6 months single payment 1xweek";
                }
                if($user_plan == "6"){
                    $user_plan = "Montly 1xweek";
                }
                if($user_plan == "7"){
                    $user_plan = "Personal Trainer Brazilian Jiu Jitsu (24 hours)";
                }
                if($user_plan == "8"){
                    $user_plan = "Brazilian Jiu Jitsu Groeplessen (26 hours)";
                }
                if($user_plan == "9"){
                    $user_plan = "Personal Trainer Brazilian Jiu Jitsu (12 hours)";
                }
                if($user_plan == "10"){
                    $user_plan = "Personal Trainer Brazilian Jiu Jitsu (30 hours)";
                }
                if($user_plan == "11"){
                    $user_plan = "Personal Trainer Brazilian Jiu Jitsu (8 hours)";
                }
                if($user_plan == "12"){
                    $user_plan = "Brazilian Jiu Jitsu 1x week Kinderen";
                }
                if($user_pat_name == "not apliable"){
                    $user_pat_name = "-";
                }


                ?>
                <tr>
                    <td>
                        <!--EDIT BUTTON-->
                        <a href="user_table.php?edit=<?php echo $user_id; ?>" class="btn btn-xs btn-default" ><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
                        <!--DELETE BUTTON-->
                        <a onclick="return confirm('Ben je zeker?')" href="user_table.php?delete=<?php echo $user_id; ?>" class="btn btn-xs btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>
                    </td>
                    <td><?php echo $user_id; ?></td>
                    <td><?php echo $user_name; ?></td>
                    <td><?php echo $user_pat_name; ?></td>
                    <td><?php echo $user_email; ?></td>
                    <td><?php echo $user_address; ?></td>
                    <td><?php echo $user_city; ?></td>
                    <td><?php echo $user_zipcode; ?></td>
                    <td><?php echo $user_phone; ?></td>
                    <td><?php echo $user_bday; ?></td>
                    <td><?php echo $user_sdate; ?></td>
                    <td><?php echo $user_plan; ?></td>
                </tr>

            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-1"></div>
</div>

<br><br><br><br><br><br><br><br><br><br><br><br>

<!--footer-->
<div class="row">
    <footer>
        <h3>Gemaakt door Mateus &copy; 2017 - <?php echo date("Y"); ?></h3>
    </footer>
</div><!--eind row-->

</div>

<script src="../bootstrap/js/jquery-1.9.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js" type="text/javascript" type="text/javascript"></script>
<script src="../js/main.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script>
    //calls the function from the dataTable with the id from the table
    $(document).ready(function(){
        $('#employee_data').DataTable();
    });
</script>

</body>
</html>

