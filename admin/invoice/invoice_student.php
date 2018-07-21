<?php
session_start();

//require the classes
require_once '../User.php';

//create the instances
$user_home = new User();

//check if user is loged in
if(!$user_home->is_logged_in()){
    $user_home->redirect('../../index.php');
}

if(isset($_POST['submit'])) {
    $user_id = $_POST['user'];
    $plan_id = $_POST['plan'];
    $factuur_nummer = $_POST['factuur'];
    $factuur_date = $_POST['factuur_date'];
    if(!empty($_POST['paid'])){
        $paid = $_POST['paid'];
    }
    else{
        $paid = '';
    }
    if($paid == 'on'){
        $pay_db = 1;
    }
    else{
        $pay_db = 0;
    }
    //insert into payments table
    $stmt_payment= $user_home->runQuery("INSERT INTO payments (user_id, factuur_nummer, payment_date, paid) 
                                              VALUES (:uid, :factuur, :pdate,:paid)");
    $stmt_payment->bindparam(":uid",$user_id);
    $stmt_payment->bindparam(":factuur",$factuur_nummer);
    $stmt_payment->bindparam(":pdate",$factuur_date);
    $stmt_payment->bindparam(":paid",$pay_db);
    $stmt_payment->execute();


    $factuur_due = new Datetime($factuur_date);
    $factuur_due->add(new DateInterval('P1M'));
    $formattedDate = $factuur_due->format('d/m/Y');


    $duration = new Datetime($factuur_date);


    $factuur_date = date("d/m/Y", strtotime($factuur_date));

    //select user
    $stmt_user = $user_home->runQuery("SELECT * FROM tbl_users WHERE id=:uid");
    $stmt_user->execute(array(":uid"=>$user_id));
    $row_user = $stmt_user->fetch(PDO::FETCH_ASSOC);
    //user name for invoice
    $user_name = $row_user['full_name'];
    //invoice name
    $invoice_name = $user_name."_".$factuur_nummer;
    //take out spaces out
    $invoice_name = str_replace(' ', '', $invoice_name);



    //select plan
    $stmt_plan = $user_home->runQuery("SELECT * FROM plan WHERE type=:pid");
    $stmt_plan->execute(array(":pid"=>$plan_id));
    $row_plan = $stmt_plan->fetch(PDO::FETCH_ASSOC);

    //Get price and information from the plan    
    $price = 169.81;
    $btw = 10.19;
    $btw21 = 0.0;
    $total = 180.00;
    $duration->add(new DateInterval('P6M'));
    $duration = $duration->format('d/m/Y');
    $btw_fac = 6;

    //start the pdf
    include('../../pdf_master/phpinvoice.php');
    $invoice = new phpinvoice('A4', '€', 'nl');
    /* Header Settings */
    $invoice->setLogo("../../img/download.png");
    $invoice->setColor("#007fff");
    $invoice->setType("Factuur");
    $invoice->setReference("N. ".$factuur_nummer);
    $invoice->setDate($factuur_date);
//$invoice->setTime(date('h:i:s A',time()));
    $invoice->setDue($formattedDate);

    $invoice->setFrom(array(
        "Mateus Gemignani", "SOMEWHERE - Amsterdam, XXXXX", "KvK nr: XXXXXXXXX", "Btw nr: XXXXXXXX", "IBAN: XXXXXXXXXXXXX"
    ));
    $invoice->setTo(array($row_user['full_name'], $row_user['email'], $row_user['address'], $row_user['city'].$row_user['zipcode'], $row_user['phone']));

    /* Adding Items in table */
    $invoice->addItem(
        $plan_id, "DATUM: " . $factuur_date . " - " . $duration,
        1, $btw21, $price, 0, $total
    );

    /* Add totals */
    $invoice->addTotal("Totaal excl. btw", $price);
    $invoice->addTotal("BTW 6%", $btw);
    $invoice->addTotal("BTW 21%", $btw21);
    $invoice->addTotal("Te betalen", $total, true);

    if ($paid == "on") {
        /* Set badge */
        $invoice->addBadge("Betaald");
    }

    //title
    $invoice->addTitle("Belangrijk Informatie");
    /* Add Paragraph */
    $invoice->addParagraph("Wij verzoeken u vriendelijk het verschuldigde bedrag van ".$total." euros binnen 30 dagen over te maken naar IBAN: XXXXXXXXXXXXX  ten name van xxxxxxx onder vermelding van factuurnummer: ".$factuur_nummer);
    $invoice->addParagraph("Op alle diensten zijn onze algemene voorwaarden van toepassing. Deze kunt u downloaden van onze website.");

    /* Add title */
    $invoice->addTitle("Contact");
    /* Add Paragraph */
    $invoice->addParagraph("Email: ");
    $invoice->addParagraph("Tel: ");
    $invoice->addParagraph("Website: ");
    /* Set footer note */
    $invoice->setFooternote("Mateus Gemignani");
    /* Render */
    $invoice->render('../factuur_files/'.$invoice_name.'.pdf', 'F'); /* I => Display on browser, D => Force Download, F => local path save, S => return document path */

    //insert into factuur table
    $stmt_fac = $user_home->runQuery("INSERT INTO factuur (factuur_nummer, factuur_totaal, factuur_name, btw) VALUES (:factuur, :totaal, :fname, :btw)");
    $stmt_fac->bindparam(":factuur",$factuur_nummer);
    $stmt_fac->bindparam(":totaal",$total);
    $stmt_fac->bindparam(":fname",$invoice_name);
    $stmt_fac->bindparam(":btw",$btw_fac);
    $stmt_fac->execute();

    header('Location: ../facturen.php');

}

