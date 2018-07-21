<?php
require_once '../core/Database.php';
require_once 'User.php';

$user_home = new User();

$user_array_id = $_POST['user_id'];
$user_id = $user_array_id[0];

//$selected = $_POST['selected'];
$stmt_user = $user_home->runQuery("SELECT plan FROM tbl_users WHERE id=:uid");
$stmt_user->execute(array(":uid"=>$user_id));
$row_user = $stmt_user->fetch(PDO::FETCH_ASSOC);

$plan_id = $row_user['plan'];

$stmt_plan = $user_home->runQuery("SELECT * FROM plan WHERE id=:pid");
$stmt_plan->execute(array(":pid"=>$plan_id));
$row_plan = $stmt_plan->fetch(PDO::FETCH_ASSOC);


ob_start();
?>
    <input class="form-control" type="text" name="plan" id="plan_2" value="<?php echo $row_plan['type'];?>" readonly>

<?php
echo ob_get_clean();