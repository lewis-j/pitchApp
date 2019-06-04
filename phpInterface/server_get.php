<?php

$request = file_get_contents("php://input");

class pitcher {
	
	    // constructor
    public function __construct($id, $pitcher_name, $year, $season) {
        $this->_id = strval($id);
        $this->pitcher_name = $pitcher_name;
        $this->year = $year;
        $this->season = $season;
        
    }

	
}


	// 1. Open connection to MySQL database (using username + password)
    $mydbserver = 'localhost';
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
        echo "<h1>Database Connection Error!</h1>";
        var_dump("Database Connection Error!");
        // do I use die() to cease running my code now?
        // could do include() here possible
        die ( "<h2>Final message</h2>" );
        // or do I choose to handle this differently?
        // header "Location: error.html";
    }


if($request === "PITCHERS"){
    
    
	
	
	 try{
     
        $allData = "SELECT `pitcher_id`, `pitcher_name`, `year`, `season`
        FROM `srjc_pitcher-roster`";
        $statement = $myconn -> prepare($allData);
        $arrayObject = array();
        
        
        $statement -> execute();
        
        $statement -> bind_result($_id, $pitcherName, $year, $season);
    
        while($statement -> fetch()){
        	
       $object =	new pitcher($_id,$pitcherName, $year, $season);
        	
        	array_push($arrayObject,$object );
        	
        }
     
        
       echo json_encode($arrayObject);
        
    
                 
                 $statement -> close();
                 
              
                 
	 } catch(Exception $e) {

        echo $e;
        var_dump("Fetching Connection Error!");

        die ( "<h2>Final message</h2>" );
        // or do I choose to handle this differently?
        // header "Location: error.html";
    }
	
    
}

?>