<?php
include "../pitchTrackWorkbench-ver-0.9.1/php/SQL_config.php";

class Pitchers {
  public $player_id;
  public $player_name;

  function __construct($player_id,$player_name) {
    $this->player_id = $player_id;
    $this->player_name = $player_name;

  }
}

        $rosterArray = array();
	 try{
		 $sql = "SELECT `pitcher_id` , `pitcher_name`
             FROM `srjc_pitcher-roster`";

				$statement = $myconn -> prepare($sql);
				 $statement -> execute();
         $statement -> bind_result($id, $name);

         while($statement -> fetch()){


              array_push($rosterArray, new Pitchers($id, $name));
         }


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

   foreach ($rosterArray as $key => $value) {
   try {

      $sql = "UPDATE `srjc_game-pitchers`
              SET `pitcher_id` = ?
              WHERE `pitcherName` = ?";
       $statement = $myconn -> prepare($sql);
       $statement -> bind_param('is',$value->player_id ,$value->player_name);
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
  }
$statement -> close();


    // 5. Close connection
    $myconn -> close();

?>
