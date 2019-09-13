<?php
session_start();

$mydbserver = 'localhost';
$mydbname = 'baseball_app';
$mydbuser = 'root';
$mydbpass = 'root';




mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


 try {
   $myconn = new mysqli(
            $mydbserver,
            $mydbuser,
            $mydbpass,
            $mydbname
        );

 }
 catch( Exception $e){

    include "e_message.inc.php";

 }





if($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_SESSION['user_id'])){


try{
    $myQuery = "SELECT `id`
                FROM `users`
                WHERE `username` = ?
                AND `password` = ?";



    $myStatment = $myconn -> prepare($myQuery);

    $myStatment -> bind_param('ss',$username = $_POST['username'], $_POST['password']);

    $myStatment -> execute();

    $myStatment -> bind_result($userid);

    if($myStatment -> fetch()){
        $_SESSION['user_id'] = $userid;
    }else{
      throw new Exception("Oops! Looks like that login information is incorrect!");
    }
    $myStatment -> close();
}catch(Exception $e){

      include "e_message.inc.php";
}

}

if( !isset($_SESSION['user_id'])){

    $myconn -> close();

    session_destroy();


      header("Location: ../login.html");

    exit;

}




?>
