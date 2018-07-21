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

//include head and navbar
include_once 'includes_admin/head_admin.php';
include_once 'includes_admin/nav_admin.php';

?>

    <br><br><br>
    <h1 class="text-center">Payments</h1>
    <br><br>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <?php if(isset($msg)) echo $msg;  ?>
            <form action="invoice/invoice_student.php" method="post">

                <?php
                //select all users
                $stmt_user = $user_home->runQuery("SELECT * FROM tbl_users WHERE permissions=0");
                $stmt_user->execute();

                //select all plan types
                $stmt_plan = $user_home->runQuery("SELECT * FROM plan");
                $stmt_plan->execute();

                $stmt_fac = $user_home->runQuery("SELECT factuur_nummer FROM factuur ORDER BY factuur_nummer DESC LIMIT 1");
                $stmt_fac->execute();
                $row_fac = $stmt_fac->fetch(PDO::FETCH_ASSOC);
                $factuur_nummer = $row_fac['factuur_nummer'];
                $new_factuur_nummer = intval($factuur_nummer) + 1;


                ?>

                <!--SELECT USER NAME-->
                <div class="form-group col-md-6">
                    <label for="user">User name*</label>
                    <select multiple class="form-control" id="user" name="user">
                        <option value="" selected></option>
                        <?php while($row_user = $stmt_user->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $row_user['id']; ?>">
                            <?php echo $row_user['full_name']; ?>
                        </option>
`                       <?php endwhile; ?>
                    </select>
                </div>

                <!--SELECT PLAN-->
                <div class="form-group col-md-6">
                    <label for="plan_2"  class="col-2 col-form-label">Abbonemment</label>
                    <div id="plan"><!--FETCH DATA WITH AJAX--></div>
                </div>


                <!--CREATE FACTUUR NUMMER-->
                <div class="form-group col-md-6">
                    <label for="factuur" class="col-2 col-form-label">Factuur Nummer</label>
                    <div class="col-10">
                        <input class="form-control" type="text" value="<?php echo $new_factuur_nummer; ?>" id="factuur" name="factuur">
                    </div>
                </div>

                <!--FACTUUR DATE-->
                <div class="form-group col-md-6">
                    <label for="factuur_date" class="col-2 col-form-label">Factuur Date</label>
                    <div class="col-10">
                        <input class="form-control" type="date" value="" id="factuur_date" name="factuur_date">
                    </div>
                </div>

                <!--PAYED-->
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" value="on" name="paid">
                        Paid
                    </label>
                </div>

                <!--SUBMIT BUTTON-->
                <button class="btn btn-large btn-primary" type="submit" name="submit">Create Invoice</button>
            </form>
        </div><!--EIND DIV COL-MD-8-->
        <div class="col-md-2"></div>
    </div>
<script>
    $('document').ready(function () {
        get_child_options();
    });

</script>
<?php

include_once 'includes_admin/footer_admin.php';
?>

