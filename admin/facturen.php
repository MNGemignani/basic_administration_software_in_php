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

$stmt_users = $user_home->runQuery("SELECT * FROM tbl_users WHERE permissions=0");
$stmt_users->execute();


//delete factuur
if(isset($_GET['delete'])){
    $fac_id_delete = $_GET['delete'];
    $stmt_delete = $user_home->runQuery("DELETE FROM factuur WHERE factuur_nummer=:fname");
    $stmt_pay_delete = $user_home->runQuery("DELETE FROM payments WHERE factuur_nummer=:fname");

    if($stmt_delete->execute(array(":fname"=>$fac_id_delete)) & $stmt_pay_delete->execute(array(":fname"=>$fac_id_delete))){
        ?>
        <!--success message and redirection to the admin page-->
        <script>
            alert('Succes deleted');
            window.location.href='facturen.php';
        </script>

        <?php
    }
    else{
        ?>
        <!--error message and redirection to the admin page-->
        <script>
            alert('An error has ocurred trying to delete the data');
            window.location.href='facturen.php';
        </script>

        <?php
    }

}

//includes
include_once 'includes_admin/head_admin.php';
include_once 'includes_admin/nav_admin.php';
?>
<br><br><br>
    <h1 class="text-center">Facturen</h1>

    <table id="employee_data" class="table table-bordered table-condensed table-striped">
        <thead>
            <th></th>
            <th>Student Name</th>
            <th>Fuctuur Nummer</th>
            <th>Factuur Date</th>
            <th>Order Status</th>
            <th>Invoice</th>
            <th>Email to Student</th>
            <th>Reminders</th>
        </thead>
        <tbody>
        <?php
        while ($row_users = $stmt_users->fetch(PDO::FETCH_ASSOC)):


            if($stmt_users->rowCount() ==0){
                ?>
                <tr>

                </tr>
                <?php
            }
            else{
                $stmt_pay = $user_home->runQuery("SELECT * FROM payments WHERE user_id=:uid");
                $stmt_pay->execute(array(":uid"=>$row_users['id']));
                if($stmt_pay->rowCount() >0) {


                while($row_pay = $stmt_pay->fetch(PDO::FETCH_ASSOC)):


                $stmt_factuur = $user_home->runQuery("SELECT * FROM factuur WHERE factuur_nummer=:fname");
                $stmt_factuur->execute(array(":fname"=>$row_pay['factuur_nummer']));
                $row_factuur = $stmt_factuur->fetch(PDO::FETCH_ASSOC);
                $pay_day = $row_pay['payment_date'];


                ?>
                <tr>
                    <td>
                       <!--DELETE BUTTON-->
                        <a onclick="return confirm('Ben je zeker?')" href="facturen.php?delete=<?php echo $row_pay['factuur_nummer']; ?>" class="btn btn-xs btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>
                    </td>
                    <td><?php echo $row_users['full_name']; ?></td>
                    <td><?php echo $row_pay['factuur_nummer']; ?></td>
                    <td><?php echo $pay_day; ?></td>
                    <td>
                        <?php echo(( $row_pay['paid']== 1) ? 'Order Payed' : 'Waiting for Payment'); ?>
                        <?php if($row_pay['paid']== 0): ?>
                            <a class="btn btn-sm btn-primary" href="invoice/pay_factuur.php?factuur=<?php echo $row_pay['factuur_nummer'];?>&user=<?php echo $row_users['id']; ?>">Paid</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="invoice/show_invoice.php?factuur=<?php echo $row_factuur['factuur_name']; ?>"
                           class="btn btn-primary ">
                            <span class="glyphicon glyphicon-download-alt"></span>
                            Invoice
                        </a>
                    </td>
                    <td>
                        <a onclick="return confirm('Ben je zeker? This will send a email to the user')" href="email/email_student.php?email=<?php echo $row_users['email'];?>&factuur=<?php echo $row_factuur['factuur_name']; ?>"
                           class="btn btn-success ">
                            <span class="glyphicon glyphicon-envelope"></span>
                            Email
                        </a>
                    </td>

                    <td>
                    <?php
                    $factuur_due = new Datetime($pay_day);
                    $factuur_5_days = new Datetime($pay_day);
                    $factuur_due->add(new DateInterval('P1M'));
                    $factuur_5_days->add(new DateInterval('P25D'));
                    $today = new DateTime();
                    if($today > $factuur_due & $row_pay['paid']== 0):
                    ?>
                        <a onclick="return confirm('Ben je zeker? This will send a email to the user')" href="email/email_reminder.php?email=<?php echo $row_users['email'];?>&factuur=<?php echo $row_factuur['factuur_name']; ?>"
                           class="btn btn-danger">
                            <span class="glyphicon glyphicon-piggy-bank"></span>
                            Due Day Reminder
                        </a>
                    <?php
                    //reminder 5 days
                    elseif($today == $factuur_5_days & $row_pay['paid']== 0):
                        ?>
                        <a onclick="return confirm('Ben je zeker? This will send a email to the user')" href="email/email_reminder_5.php?email=<?php echo $row_users['email'];?>&factuur=<?php echo $row_factuur['factuur_name']; ?>"
                           class="btn btn-danger">
                            <span class="glyphicon glyphicon-piggy-bank"></span>
                            5 days Before Reminder
                        </a>
                    <?php elseif($row_pay['paid']== 1): ?>
                        <p>Already Paid</p>
                    <?php else: ?>
                        <p>Still on time</p>
                    <?php endif; ?>
                    </td>
                </tr>


            <?php endwhile;}
            }
        endwhile; ?>
        </tbody>
    </table>
<?php
include_once 'includes_admin/footer_admin.php';
