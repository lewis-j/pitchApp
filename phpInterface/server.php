<?php
$request = file_get_contents("php://input");
$object = json_decode($request);
include "../pitchTrackWorkbench-ver-0.9.1/php/custom_error.inc.php";
include "../pitchTrackWorkbench-ver-0.9.1/php/SQLConnect.inc.php";

$objType = $object->objType;
	$_id = NULL;
echo "here is the incoming object";
var_dump($object);

	var_dump("server.php objType 1");
	$date = $object->date;
	$time = $object->timeStamp;
	$opponent = $object->opponent;
	$gameNumber = $object->gameNum;
	$pitcherName = $object->playerName;
	$startingPitcher = $object->startingPitcher;
	$gameType = $object -> gameType;// PlayerData from UI

// 1. Open connection to MySQL database (using username + password)
$mydbserver = 'localhost:3306';
$mydbname = 'lindsgp8_Baseball_Pitch_App';
$mydbuser = 'lindsgp8_lindsgp';
$mydbpass = 'Lubertson$27';

    // NEW: ERROR HANDLING WITH MYSQLI mysqli_report()
    // NOW I USE try {} catch() {} to INTERCEPT ERRORS
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    //mysqli_report(MYSQLI_REPORT_ALL);

    // Step 1 of our SQL 5-step program:
    // when we open a connection to mysql we have to keep track of the connection in a variable
    try {
        $myconn = new mysqli(
            $mydbserver,
            $mydbuser,
            $mydbpass,
            $mydbname
        );
    }
    catch(Exception $e) {
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

   echo "<h1>Database Connection Success!</h1>";
   var_dump("Database Connection Success!");

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
