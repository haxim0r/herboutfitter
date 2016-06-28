<?php

if(!isset($_SESSION)){

    session_start();
    //header("Access-Control-Allow-Origin: http://1.2.3.4");
}

if(!isset($_SESSION['auth'])){

    if(isset($_POST['esmet']) && isset($_POST['ejazat'])){

        ini_set("session.cookie_domain", ".capella.rezapps.com");

        $username = isset($_POST["esmet"])?strtolower($_POST["esmet"]):''; //remove case sensitivity on the username
        $password = isset($_POST["ejazat"])?$_POST["ejazat"]:'';

        unset($_SESSION['auth']);

        if($username != NULL && $password != NULL){
            
            $mySQL = new db_mysql();

            $sql = 'select * from user where username=\''.$username.'\' and password=\''.$password.'\'';

            if($result = mysqli_query($mySQL->connection, $sql)) {

                while ($row = mysqli_fetch_assoc($result)) {

                    $_SESSION['auth']["id"]        = $row['id'];
                    $_SESSION['auth']["username"]  = $row['username'];
                    $_SESSION['auth']["password"]  = $row['password'];
                    $_SESSION['auth']["firstname"] = $row['firstname'];
                    $_SESSION['auth']["lastname"]  = $row['lastname'];
                    $_SESSION['auth']["email"]     = $row['email'];
                    $_SESSION['auth']["phone"]     = $row['phone'];
                    $_SESSION['auth']["role"]      = $row['role'];
                }

                mysqli_free_result($result);
                
                header('Location: /');
            }
        }
    }
}



if(isset($_GET['logout'])){

    unset($_SESSION['auth']);

    session_destroy();

    //echo "<script type='text/javascript'>top.location.reload()</script>";
    //exit;
    header('location: /');
}
?>
