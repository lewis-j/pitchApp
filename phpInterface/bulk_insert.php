<?php
$requestPayload = file_get_contents("php://input");

$object = json_decode($requestPayload);

$_id = NULL;


// 1. Open connection to MySQL database (using username + password)
$mydbserver = 'localhost:3306';
$mydbname = 'baseball_app';
$mydbuser = 'root';
$mydbpass = 'root';
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
	// PlayerData from UI
	 try{
			// pitchData from UI
	 // Query database to get last game_pitchers table entry.
	 // Need pitchers_id to make foreign fk_pitchers_id entry in
	 //   game_pitches table.
	 $sql = "SELECT `pitchers_id` , `pitcherName`
							FROM `srjc_game-pitchers`
							ORDER BY pitchers_id DESC
							LIMIT 1";
	$statement = $myconn -> prepare($sql);
	$statement -> execute();
    $statement -> bind_result($pid, $pName);
    if($statement -> fetch()){
    	$statement -> close();
    	$sqlGetPitch = "INSERT INTO `$mydbname`.`srjc_game-pitches`  (
			`pitches_id` ,
			`fk_pitchers_id` ,
			`time`,
			`pitchspeed` ,
			`pitchCount` ,
			`batterhandness` ,
			`endPlay` ,
			`firstpitch` ,
			`pitchType` ,
			`play` ,
			`xCoord` ,
			`yCoord`
			)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

			$pitchStatement = $myconn -> prepare($sqlGetPitch);

			foreach($object as $value){
			$pitchStatement -> bind_param("iisiisssssdd",$_id,$pid,$value -> timeStamp, $value-> pitchSpeed,$value ->gameCount->pitchCount,$value->batterHandedness,$value->endPlay,$value->firstPitch,$value->pitchType,$value->play,$value->xCoord,$value->yCoord);
			$pitchStatement -> execute();
			}

			$pitchStatement ->close();


    }else{
   $statement -> close();
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



 $myconn-> close();

?>
