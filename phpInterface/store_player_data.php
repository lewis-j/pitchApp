<?php
$request = file_get_contents("php://input");
$object = json_decode($request);

$objType = $object->objType;
	$_id = NULL;

	$date = $object->date;
	$time = $object->timeStamp;
	$opponent = $object->opponent;
	$gameNumber = $object->gameNum;
	$pitcherName = $object->playerName;
	$startingPitcher = $object->startingPitcher;
	$gameType = $object -> gameType;// PlayerData from UI

include "../pitchTrackWorkbench-ver-0.9.1/php/SQL_config.php";

	 // insert into game_pitchers table.
	 if ($objType == "1") {	// PlayerData from UI
	 try{
		 $sql = "INSERT INTO `$mydbname`.`srjc_game-pitchers`  (
				`pitchers_id` ,
				`date`,
				`time`,
				`opponent` ,
				`gameNumber` ,
				`pitcherName` ,
				`startingPitcher` ,
				`gameType`
				)
				VALUES (?,?,?,?,?,?,?,?)";

				$statement = $myconn -> prepare($sql);
				 $statement -> bind_param("isssssss",$_id ,$date, $time, $opponent, $gameNumber, $pitcherName, $startingPitcher, $gameType);
				 $statement -> execute();
                 $statement -> close();

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



    // 5. Close connection
    $myconn -> close();

?>
