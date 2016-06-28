<?php

require_once 'global.php';

class ajax{

    public $action;
    public $input;
    public $output;

    function __construct(){

    }
}

if(isset($_GET['ajax'])){

    switch($_GET['ajax']){

        case 'image':

            if(isset($_GET["id"]) && is_numeric($_GET["id"])){

                $mySQL = new db_mysql();
                $sql = "SELECT type, image FROM site_bg WHERE id=".$_GET["id"];
                $result = mysqli_query($mySQL->connection, $sql);

                while($row = $result->fetch_assoc()) {

                    $daImage_type = $row["type"];
                    $daImage = base64_decode($row["image"]);
                }

                mysqli_free_result($result);

                //header("Cache-Control: max-age=2592000"); //30days (60sec * 60min * 24hours * 30days)
                header("Pragma: public");
                //header("Expires: 0");
                //header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: public"); 

                header("Content-type: ".$daImage_type);
                echo $daImage;
            }
            exit;
        case 'bgList_sm':

            $mySQL = new db_mysql();

            $resultset = mysqli_query($mySQL->connection, "select id from site_bg where dimensions='small'");

            if($resultset){

                $resultArray = array();
                while ($row = $resultset->fetch_assoc()) {

                    $resultArray[] = $row;
                }
                mysqli_free_result($resultset);

                $darth_message = str_replace('"{', '{', json_encode($resultArray));
                $darth_message = str_replace('}"', '}', $darth_message);
                $darth_message = str_replace('\\', '', $darth_message);

                echo $darth_message;
            }
            exit;
        case 'bgList_lg':

            $mySQL = new db_mysql();

            $resultset = mysqli_query($mySQL->connection, "select id from site_bg where dimensions='large'");

            if($resultset){

                $resultArray = array();
                while ($row = $resultset->fetch_assoc()) {

                    $resultArray[] = $row;
                }
                mysqli_free_result($resultset);

                $darth_message = str_replace('"{', '{', json_encode($resultArray));
                $darth_message = str_replace('}"', '}', $darth_message);
                $darth_message = str_replace('\\', '', $darth_message);

                echo $darth_message;
            }
            exit;
        case 'fullcalendar':

            $mySQL = new db_mysql();

            $resultset = mysqli_query($mySQL->connection, 'select title, DATE(start) `start`, DATE(end) `end` from event');

            if($resultset){

                $resultArray = array();
                while ($row = $resultset->fetch_assoc()) {

                    $resultArray[] = $row;
                }
                mysqli_free_result($resultset);

                $darth_message = str_replace('"{', '{', json_encode($resultArray));
                $darth_message = str_replace('}"', '}', $darth_message);
                $darth_message = str_replace('\\', '', $darth_message);

                echo $darth_message;
            }
            exit;
        case 'userEvents':

            $mySQL = new db_mysql();

            $resultset = mysqli_query($mySQL->connection, 'select r.id `id`, et.title, DATE(r.event_date) `start`, DATE(r.event_date) `end` '
                    . 'from registration r inner join event_type et on et.id=r.event_type '
                    . 'where r.user='.$_GET["id"].';');

            if($resultset){

                $resultArray = array();
                while ($row = $resultset->fetch_assoc()) {

                    $resultArray[] = $row;
                }
                mysqli_free_result($resultset);

                $darth_message = str_replace('"{', '{', json_encode($resultArray));
                $darth_message = str_replace('}"', '}', $darth_message);
                $darth_message = str_replace('\\', '', $darth_message);

                echo $darth_message;
            }
            exit;
        default:
            
            exit;
    }
}
elseif(isset($_POST['ajax'])){

    if($_POST['ajax'] === 'new_reg'){

        $event_type = filter_input(INPUT_POST, 'event_type', FILTER_SANITIZE_MAGIC_QUOTES);
        $event_date = filter_input(INPUT_POST, 'event_date', FILTER_SANITIZE_MAGIC_QUOTES);
        $email1 = filter_input(INPUT_POST, 'email1', FILTER_SANITIZE_MAGIC_QUOTES);
        $firstname1 = filter_input(INPUT_POST, 'firstname1', FILTER_SANITIZE_MAGIC_QUOTES);
        $lastname1 = filter_input(INPUT_POST, 'lastname1', FILTER_SANITIZE_MAGIC_QUOTES);
        $phone1 = filter_input(INPUT_POST, 'phone1', FILTER_SANITIZE_MAGIC_QUOTES);

        $mySQL = new db_mysql();

        $sql_outer = "select id from user where email='".$email1."'";

        $resultset = mysqli_query($mySQL->connection, $sql_outer);

        $newRegistrationId = 0;

        if(mysqli_num_rows($resultset) > 0){

            $user = mysqli_fetch_object($resultset);

            $sql = 'insert into registration(user, event_type, event_date) values ('.$user->id.', '.$event_type.', "'.$event_date.'");';

            mysqli_query($mySQL->connection, $sql);

            $newRegistrationId = mysqli_insert_id($mySQL->connection);
        }
        else{

            $sql = "insert into user (username, password, firstname, lastname, email, phone, role, status) values (".
                   "'".$email1."', ".
                   "'referral', ".
                   "'".$firstname1."', ".
                   "'".$lastname1."', ".
                   "'".$email1."', ".
                   "'".$phone1."', ".
                   "'referral', ".
                   "'active');";

            mysqli_query($mySQL->connection, $sql);

            $new_user_id = mysqli_insert_id($mySQL->connection);

            $sql = 'insert into registration(user, event_type, event_date) values ('.$new_user_id.', '.$event_type.', "'.$event_date.'");';

            mysqli_query($mySQL->connection, $sql);

            $newRegistrationId = mysqli_insert_id($mySQL->connection);
        }

        $registreeCount = 1;

        for($counter = 1; $counter <= $_POST["registreeCount"]; $counter++){

            $email = filter_input(INPUT_POST, 'email'.$counter, FILTER_SANITIZE_MAGIC_QUOTES);
            $firstname = filter_input(INPUT_POST, 'firstname'.$counter, FILTER_SANITIZE_MAGIC_QUOTES);
            $lastname = filter_input(INPUT_POST, 'lastname'.$counter, FILTER_SANITIZE_MAGIC_QUOTES);
            $phone = filter_input(INPUT_POST, 'phone'.$counter, FILTER_SANITIZE_MAGIC_QUOTES);

            $sql_inner = "select id from user where email='".$email."'";

            $resultset = mysqli_query($mySQL->connection, $sql_inner);

            if(mysqli_num_rows($resultset) > 0){

                $user = mysqli_fetch_object($resultset);

                $sql = 'insert into reg_user(registration, user, status) values ('.$newRegistrationId.', '.$user->id.', "confirmed");';

                mysqli_query($mySQL->connection, $sql);
            }
            else{

                $sql = "insert into user (username, password, firstname, lastname, email, phone, role, status) values (".
                       "'".$email."', ".
                       "'referral', ".
                       "'".$firstname."', ".
                       "'".$lastname."', ".
                       "'".$email."', ".
                       "'".$phone."', ".
                       "'referral', ".
                       "'active');";

                mysqli_query($mySQL->connection, $sql);

                $new_user_id = mysqli_insert_id($mySQL->connection);

                $sql = 'insert into reg_user(registration, user, status) values ('.$newRegistrationId.', '.$new_user_id.', "confirmed");';
                mysqli_query($mySQL->connection, $sql);
            }
        }

        $mySQL->disconnect();
        //echo "registration successful.";
        exit;
    }
    elseif($_POST['ajax'] === 'recover'){
        
        $email = filter_input(INPUT_POST, 'pw_user', FILTER_SANITIZE_MAGIC_QUOTES);
        
        $mySQL = new db_mysql();

        $sql = "select firstname, password from user where email='".$email."'";

        $resultset = mysqli_query($mySQL->connection, $sql);

        if(mysqli_num_rows($resultset) > 0){

            $user = mysqli_fetch_object($resultset);
            
            $to      = $email;
            $subject = 'Herb\'n Outfitter - password recovery';
            $message = 'Hello '.$user->firstname.'. Your password is: '.$user->password;
            $headers = 'From: noreply@rezapps.com' . "\r\n" .
                'Reply-To: noreply@rezapps.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);
            
            echo "alert('Password has been sent to ".$email."');";
        }
        else{
            
            echo "alert('".$email." not present.');";
        }
        
        $mySQL->disconnect();
        exit;
    }
    elseif($_POST['ajax'] === 'subscribe'){
        
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_MAGIC_QUOTES);
        
        $mySQL = new db_mysql();

        $sql = "select id, subscribed from user where email='".$email."'";

        $resultset = mysqli_query($mySQL->connection, $sql);

        if(mysqli_num_rows($resultset) > 0){

            $user = mysqli_fetch_object($resultset);

            $sql = 'update user set subscribed=\'yes\' where id='.$user->id;

            mysqli_query($mySQL->connection, $sql);
            
            //echo "alert('Please check ".$email." for newsletter confirmation.');";
        }
        else{

            $sql = "insert into user (subscribed, username, password, firstname, lastname, email, phone, role, status) values (".
                   "'yes', ".
                   "'".$email."', ".
                   "'referral', ".
                   "'".$firstname."', ".
                   "'".$lastname."', ".
                   "'".$email."', ".
                   "'".$phone."', ".
                   "'referral', ".
                   "'active');";

            mysqli_query($mySQL->connection, $sql);
            
            //echo "alert('Welcome to Herb\'n Outfitter and thanks for your subscription.');";
        }
        
        $mySQL->disconnect();
        
        echo "alert('Welcome to Herb\'n Outfitter and thanks for your subscription.');";
        exit;
    }

    if(!isset($_SESSION)){

        session_start();
    }
    
    if(isset($_SESSION["auth"])){

        switch($_POST['ajax']){

            case 'SQL':

                $mySQL = new db_mysql();

                $resultset = mysqli_query($mySQL->connection, urldecode($_POST['statement']));

                if($resultset){

                    //$resultArray = mysqli_fetch_all($resultset);
                    $resultArray = array();
                    while ($row = $resultset->fetch_assoc()) {

                        $resultArray[] = $row;
                    }
                    mysqli_free_result($resultset);

                    //echo json_encode($resultArray);
                    $darth_message = str_replace('"{', '{', json_encode($resultArray));
                    $darth_message = str_replace('}"', '}', $darth_message);
                    $darth_message = str_replace('\\', '', $darth_message);

                    echo $darth_message;
                }
                exit;
                
            case 'update_user':
                
                $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_MAGIC_QUOTES);
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_MAGIC_QUOTES);
                $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_MAGIC_QUOTES);
                $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_MAGIC_QUOTES);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_MAGIC_QUOTES);
                $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_MAGIC_QUOTES);
                $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_MAGIC_QUOTES);
                $zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_MAGIC_QUOTES);
                
                $sql = "update user set username='".$email."', ".
                       "password='".$password."', ".
                       "firstname='".$firstname."', ".
                       "lastname='".$lastname."', ".
                       "email='".$email."', ".
                       "phone='".$phone."', ".
                       "address='".$address."', ".
                       "city='".$city."', ".
                       "zip='".$zip."', ".
                       "role='visitor', ".
                       "status='active' where id='".$id."';";
                
                $mySQL = new db_mysql();
                error_log($sql);
                mysqli_query($mySQL->connection, $sql);
                
                $mySQL->disconnect();
                
                echo "user successfully created.";
                exit;

            default:
                
                exit;
        }
    }
    else{
        
        switch($_POST['ajax']){
            
            case 'create_user':
                
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_MAGIC_QUOTES);
                $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_MAGIC_QUOTES);
                $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_MAGIC_QUOTES);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_MAGIC_QUOTES);
                $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_MAGIC_QUOTES);
                $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_MAGIC_QUOTES);
                $zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_MAGIC_QUOTES);
                
                $sql = "insert into user (username, password, firstname, lastname, email, phone, address, city, zip, role, status) values (".
                       "'".$email."', ".
                       "'".$password."', ".
                       "'".$firstname."', ".
                       "'".$lastname."', ".
                       "'".$email."', ".
                       "'".$phone."', ".
                       "'".$address."', ".
                       "'".$city."', ".
                       "'".$zip."', ".
                       "'visitor', ".
                       "'active');";
                
                $mySQL = new db_mysql();
                //error_log($sql);
                mysqli_query($mySQL->connection, $sql);
                
                $mySQL->disconnect();
                
                echo "user successfully created.";
                exit;

            default:
                
                exit;
        }
    }
}
?>
