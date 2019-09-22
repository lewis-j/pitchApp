<?php
include "../pitchTrackWorkbench-ver-0.9.1/php/SQL_config.php";

$id = 1;
$name = "Trenton Darley (P)";
   try {

      $sql = "UPDATE `srjc_game-pitchers`
              SET `pitcher_id` = ?
              WHERE `pitcherName` = ?";
       $statement = $myconn -> prepare($sql);
       $statement -> bind_param('is',$id, $name);
       $statement -> execute();

   } catch(Exception $e) {
        // echo "<pre>";
        // print_r($e);
        // echo "</pre>";
        echo "<h1>Database Connection Error!</h1>";
        var_dump("Database Connection Error!");
        // do I use die() to cease running my code now?
        // could do include() here possible
        die ( "<h2>Final message</h2>" );
        // or do I choose to handle this differently?
        // header "Location: error.html";
    }

$statement -> close();


    // 5. Close connection
    $myconn -> close();

?>
