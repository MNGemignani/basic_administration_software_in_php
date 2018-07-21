<?php

require $_SERVER[DOCUMENT_ROOT] . 'simple_admin/core/Database.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class User
{

    private $conn;

    public function __construct()
    {
        $database = new Database();
        $db = $database->dbConnection();
        $this->conn = $db;
    }

    public function runQuery($sql)
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }

    public function lasdID()
    {
        $stmt = $this->conn->lastInsertId();
        return $stmt;
    }

    public function register($full_name,$full_name_pat,$email,$password,$bday,$address,$city,$zipcode,$phone,$sdate,$plan,$permissions)
    {
        try
        {
            $password = md5($password);
            $stmt = $this->conn->prepare("INSERT INTO tbl_users(full_name,full_name_pat,email,password,bday,address,city,zipcode,phone,sdate,plan,permissions) 
			                                             VALUES(:full_name, :full_name_pat, :email, :password, :bday, :address, :city, :zipcode, :phone, :sdate, :plan, :permissions)");
            $stmt->bindparam(":full_name",$full_name);
            $stmt->bindparam(":full_name_pat",$full_name_pat);
            $stmt->bindparam(":email",$email);
            $stmt->bindparam(":password",$password);
            $stmt->bindparam(":bday",$bday);
            $stmt->bindparam(":address",$address);
            $stmt->bindparam(":city",$city);
            $stmt->bindparam(":zipcode",$zipcode);
            $stmt->bindparam(":phone",$phone);
            $stmt->bindparam(":sdate",$sdate);
            $stmt->bindparam(":plan",$plan);
            $stmt->bindparam(":permissions",$permissions);

            $stmt->execute();
            return $stmt;
        }
        catch(PDOException $ex)
        {
            echo $ex->getMessage();
        }
    }

    public function edit_user($full_name,$full_name_pat,$email,$password,$bday,$address,$city,$zipcode,$phone,$sdate,$plan,$permissions,$user_id)
    {
        try
        {
            $password = md5($password);
            $stmt_edit = $this->conn->prepare(
                    "UPDATE `tbl_users` 
                            SET `full_name` = :full_name, 
                            `full_name_pat` = :full_name_pat, 
                            `email` = :email, 
                            `password` = :pass, 
                            `bday` = :bday,
                            `address` = :address, 
                            `city` = :city, 
                            `zipcode` = :zipcode, 
                            `phone` = :phone, 
                            `sdate` = :sdate, 
                            `plan` = :plan, 
                            `permissions` = :permissions
                            WHERE `id` = :uid"
            );
            $stmt_edit->bindparam(":full_name",$full_name,PDO::PARAM_STR);
            $stmt_edit->bindparam(":full_name_pat",$full_name_pat,PDO::PARAM_STR);
            $stmt_edit->bindparam(":email",$email,PDO::PARAM_STR);
            $stmt_edit->bindparam(":pass",$password,PDO::PARAM_STR);
            $stmt_edit->bindparam(":bday",$bday,PDO::PARAM_STR);
            $stmt_edit->bindparam(":address",$address,PDO::PARAM_STR);
            $stmt_edit->bindparam(":city",$city,PDO::PARAM_STR);
            $stmt_edit->bindparam(":zipcode",$zipcode,PDO::PARAM_STR);
            $stmt_edit->bindparam(":phone",$phone,PDO::PARAM_STR);
            $stmt_edit->bindparam(":sdate",$sdate,PDO::PARAM_STR);
            $stmt_edit->bindparam(":plan",$plan,PDO::PARAM_STR);
            $stmt_edit->bindparam(":permissions",$permissions,PDO::PARAM_STR);
            $stmt_edit->bindparam(":uid",$user_id,PDO::PARAM_STR);

            $stmt_edit->execute();
            return $stmt_edit;
        }
        catch(PDOException $ex)
        {
            echo $ex->getMessage();
        }
    }

    public function login($email,$upass)
    {
        try
        {
            $stmt = $this->conn->prepare("SELECT * FROM tbl_users WHERE email=:email_id");
            $stmt->execute(array(":email_id"=>$email));
            $userRow=$stmt->fetch(PDO::FETCH_ASSOC);

            if($stmt->rowCount() == 1)
            {
                if($userRow['password']==md5($upass)) {
                    $_SESSION['userSession'] = $userRow['id'];
                    return true;
                }
                else{
                    header("Location: index.php?error");
                    exit;
                }
            }
            else
            {
                header("Location: index.php?error");
                exit;
            }
        }
        catch(PDOException $ex)
        {
            echo $ex->getMessage();
        }
    }



    public function is_logged_in()
    {
        if(isset($_SESSION['userSession']))
        {
            return true;
        }
    }

    public function is_logged_admin()
    {
        if(isset($_SESSION['userSession']))
        {
            $user_id = $_SESSION['userSession'];
            $stmt = $this->conn->prepare("SELECT * FROM tbl_users WHERE id=:user_id");
            $stmt->execute(array(":user_id"=>$user_id));
            $userRow=$stmt->fetch(PDO::FETCH_ASSOC);

            if($userRow['permissions'] == 1) {
                return true;
            }
            else {
                return false;
            }

        }
    }

    public function redirect($url)
    {
        header("Location: $url");
    }

    public function logout()
    {
        session_destroy();
        $_SESSION['userSession'] = false;
    }

    function send_mail($email,$message,$subject,$atach)
    {
        // Import PHPMailer classes into the global namespace
        // These must be at the top of your script, not inside a function


        //Load composer's autoloader
        require '../vendor/autoload.php';
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = true;
        $mail->Username="<CHANGE HERE - YOUR EMAIL ADDRESS>";
        $mail->Password="<CHANGE HERE - YOUR EMAIL PASSWORD>";
        $mail->SMTPSecure = "tls";
        $mail->Host       = "smtp.gmail.com";
        $mail->Port       = 587;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->AddAddress($email);

        $mail->SetFrom('<CHANGE HERE - YOUR EMAIL ADDRESS>','<CHANGE HERE - EMAIL TITLE>');
        $mail->AddReplyTo("<CHANGE HERE - YOUR EMAIL ADDRESS>","<CHANGE HERE - EMAIL TITLE>");

        $mail->addAttachment($atach);
        $mail->Subject = $subject;
        $mail->MsgHTML($message);
        $mail->Send();
    }
}