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

if(isset($_GET['factuur'])) {
    $factuur_nummer = htmlentities($_GET['factuur'], ENT_QUOTES, "UTF-8");
    $user_id = htmlentities($_GET['user'], ENT_QUOTES, "UTF-8");

    //get information from factuur
    $stmt_fac = $user_home->runQuery("SELECT * FROM factuur WHERE factuur_nummer=:fnummer");
    $stmt_fac->execute(array(":fnummer"=>$factuur_nummer));
    $row_fac = $stmt_fac->fetch(PDO::FETCH_ASSOC);

    //get information from payments
    $stmt_payment= $user_home->runQuery("SELECT * FROM payments WHERE factuur_nummer=:fnummer");
    $stmt_payment->execute(array(":fnummer"=>$factuur_nummer));
    $row_pay= $stmt_payment->fetch(PDO::FETCH_ASSOC);

    //get information from user
    $stmt_user = $user_home->runQuery("SELECT * FROM tbl_users WHERE id=:uid");
    $stmt_user->execute(array(":uid"=>$user_id));
    $row_user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    //DATES
    $factuur_date = $row_pay['payment_date'];
    $factuur_due = new Datetime($factuur_date);
    $factuur_due->add(new DateInterval('P1M'));
    $formattedDate = $factuur_due->format('d/m/Y');
    $duration = new Datetime($factuur_date);
    $factuur_date = date("d/m/Y", strtotime($factuur_date));


    $plan_id = $row_user['plan'];
    //user name for invoice
    $user_name = $row_user['full_name'];
    //invoice name
    $invoice_name = $user_name."_".$factuur_nummer;
    //take out spaces out
    $invoice_name = str_replace(' ', '', $invoice_name);

    //Get price and information from the plan
    $price = 169.81;
    $btw = 10.19;
    $btw21 = 0.0;
    $total = 180.00;
    $duration->add(new DateInterval('P6M'));
    $duration = $duration->format('d/m/Y');
    $plan_type = '6 maanden onbeperkt training. Eenmalige Betaling.';

    //start the pdf
    include('../../pdf_master/phpinvoice.php');
    $invoice = new phpinvoice('A4', '€', 'nl');
    
    /* Header Settings */
    $invoice->setLogo("../../img/download.png");
    $invoice->setColor("#007fff");
    $invoice->setType("Factuur");
    $invoice->setReference("N. ".$factuur_nummer);
    $invoice->setDate($factuur_date);
    $invoice->setDue($formattedDate);

    $invoice->setFrom(array(
        "Mateus Gemignani", "SOMEWHERE - Amsterdam, XXXXX", "KvK nr: XXXXXXXXX", "Btw nr: XXXXXXXX", "IBAN: XXXXXXXXXXXXX"
    ));
    $invoice->setTo(array($row_user['full_name'], $row_user['email'], $row_user['address'], $row_user['city'].$row_user['zipcode'], $row_user['phone']));

    /* Adding Items in table */
    $invoice->addItem(
        $plan_type, "DATUM: " . $factuur_date . " - " . $duration,
        1, $btw21, $price, 0, $total
    );
    
    /* Add totals */
    $invoice->addTotal("Totaal excl. btw", $price);
    $invoice->addTotal("BTW 6%", $btw);
    $invoice->addTotal("BTW 21%", $btw21);
    $invoice->addTotal("Te betalen", $total, true);

    /* Set badge */
    $invoice->addBadge("Betaald");

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

    //update paid from payments table
    $stmt_pay_update= $user_home->runQuery("UPDATE `payments` SET `paid`=1 WHERE `factuur_nummer`=:factuur");
    $stmt_pay_update->execute(array(":factuur"=>$factuur_nummer));

    header('Location: ../facturen.php');
}

