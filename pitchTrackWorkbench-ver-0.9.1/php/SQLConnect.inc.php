<?php 

    $mydbserver = 'localhost';
    $mydbname = 'cloud9_oaktrail_srjc_baseball';
    $mydbuser = 'srjcjimdennis';

session_start();


mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

 
 try {
   $myconn = new mysqli(
            $mydbserver,
            $mydbuser,
            "",
            $mydbname
        );
   
 }
 catch( Exception $e){
 
    include "e_message.inc.php";

 }
 

 $credentialsExist = false;
 
   
if($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_SESSION['user_id'])){
    
    if(isset($request)){
        
     if($object -> credentials -> username){
     $username = $object -> credentials -> username;
     $password = $object -> credentials -> password;
     $credentialsExist = true;   
     }
    } else if(isset($_POST['username'])){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $credentialsExist = true; 
    }
    
    if($credentialsExist){
  
 
    $myQuery = "SELECT `id` 
                FROM `users` 
                WHERE `username` = ?
                AND `password` = ?";
                
         
                
    $myStatment = $myconn -> prepare($myQuery);
    
    $myStatment -> bind_param('ss',$username, $password);
    
    $myStatment -> execute();
    
    $myStatment -> bind_result($userid);
    
    if($myStatment -> fetch()){
        $_SESSION['user_id'] = $userid;
    }
    $myStatment -> close();
    
  
}
    
}

if( !isset($_SESSION['user_id'])){
    
    $myconn -> close();
    
    session_destroy();
    
    if(isset($request)){
       
       $false = false;
       echo json_encode($false);
    }else{
    
      header("Location: ../login.html");   
    }
    
    exit;
    
    
}else{
   if(isset($request)){
       
       $false = true;
       echo json_encode($false);
    } 
}




?>