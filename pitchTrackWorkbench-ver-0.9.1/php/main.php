
<?php
include "custom_error.inc.php";
include "SQLConnect.inc.php";


if(isset($_POST['pitchername'])){
  $pitcherName = $_POST['pitchername'];
}else{
  $pitcherName = '';
}

$colors = array("red","blue","green","purple","orange");

        
        class TableData{
    public $title;
    public $total;
    public $balls;
    public $strikes;
    public $strikePerc;
    public $minSp; 
    public $maxSp;
    public $avgSp;
    public $totalBat;

    
    
      function __construct( $title, $s, $b, $min, $max, $avg,$tB)  
    { 
      
                 $t = $s + $b;
        $this->title = $title;         
        $this->total = $t; 
        $this->balls = $b;
        $this->strikes = $s;
        $this->strikePerc = ($t != 0 )?  number_format( ($s/($t))*100 ,1 ) :  0  ;
        $this->minSp = $min;
        $this->maxSp = $max;
        $this->avgSp = number_format($avg, 1);
        $this->totalBat = $tB;
    }
}
        class Coords{
    public $x;
    public $y;
    public $t;
    
      function __construct( $x, $y, $t )  
    { 
        $this->x = $x; 
        $this->y = $y;
        $this->t = $t;
    }
}
         function createStatement($pitcherName, $option1, $option2, $date, $getOpponent){
         
          
           $pitchT = array("FB","CB","CH","SL","other");
           $dateOption = "";
          if($date != ""){
            $dateOption = "AND `srjc_game-pitchers`.`date` = '{$date}'";
          }
           

             $allData = "SELECT ";
                   if($getOpponent){
                     $allData = $allData."`srjc_game-pitchers`.`opponent`,";
                   }
              // $allData = $allData."SUM(CASE WHEN  `srjc_game-pitches`.`play` =  'Strike' THEN 1 ELSE 0 END) as 'allS',
              //       SUM(CASE WHEN  `srjc_game-pitches`.`play` =  'Ball' THEN 1 ELSE 0 END) as 'allB',";
                    if($option1 ==""){
                      $allData = $allData . "MIN(`srjc_game-pitches`.`pitchspeed`),
                                             MAX(`srjc_game-pitches`.`pitchspeed`),
                                             AVG(`srjc_game-pitches`.`pitchspeed`),";
                      
                    }else{
                       
                      
                      if($option2 != ""){
                        $option2 = "AND ".$option2;
                      }
                      
                      $allData = $allData . "MIN(CASE WHEN  {$option1} {$option2} THEN `srjc_game-pitches`.`pitchspeed` END) as 'allMinSpFB',
                                             MAX(CASE WHEN  {$option1} {$option2} THEN `srjc_game-pitches`.`pitchspeed` END) as 'allMaxSpFB',
                                             AVG(CASE WHEN  {$option1} {$option2} THEN `srjc_game-pitches`.`pitchspeed` END) as 'allAvgSpFB',";
                                          
                      $option1 = "AND ".$option1;                       
                      
                      
                    }
                   
                    
                   $allData = $allData . "
                    SUM(CASE WHEN  `srjc_game-pitches`.`play` =  'Strike' {$option1} {$option2} THEN 1 ELSE 0 END) as 'allS',
                    SUM(CASE WHEN  `srjc_game-pitches`.`play` =  'Ball' {$option1} {$option2} THEN 1 ELSE 0 END) as 'allB',
                    SUM(CASE WHEN  `srjc_game-pitches`.`firstpitch` =  '1' {$option1} {$option2} THEN 1 ELSE 0 END) as 'allbatFB',
                    SUM(CASE WHEN  `srjc_game-pitches`.`endPlay` =  'Strike Out' {$option1} {$option2} THEN 1 ELSE 0 END) as 'allK',
                    SUM(CASE WHEN  `srjc_game-pitches`.`endPlay` =  'Walk' {$option1} {$option2} THEN 1 ELSE 0 END) as 'allW',
                    SUM(CASE WHEN  `srjc_game-pitches`.`endPlay` =  'Hit' {$option1} {$option2} THEN 1 ELSE 0 END) as 'allH',";
                    
           
                    
                    for($i=0; $i < count($pitchT); $i++) {
                       $allData = $allData.
                    "SUM(CASE WHEN  `srjc_game-pitches`.`play` =  'Strike' AND `srjc_game-pitches`.`pitchType` = '{$pitchT[$i]}' {$option1} {$option2} THEN 1 ELSE 0 END) as 'allSFB',
                    SUM(CASE WHEN  `srjc_game-pitches`.`play` =  'Ball' AND `srjc_game-pitches`.`pitchType` = '{$pitchT[$i]}' {$option1} {$option2} THEN 1 ELSE 0 END) as 'allBFB',
                    MIN(CASE WHEN  `srjc_game-pitches`.`pitchType` = '{$pitchT[$i]}' {$option1} {$option2} THEN `srjc_game-pitches`.`pitchspeed` END) as 'allMinSpFB',
                    MAX(CASE WHEN  `srjc_game-pitches`.`pitchType` = '{$pitchT[$i]}' {$option1} {$option2} THEN `srjc_game-pitches`.`pitchspeed` END) as 'allMaxSpFB',
                    AVG(CASE WHEN  `srjc_game-pitches`.`pitchType` = '{$pitchT[$i]}' {$option1} {$option2} THEN `srjc_game-pitches`.`pitchspeed` END) as 'allAvgSpFB',
                    SUM(CASE WHEN  `srjc_game-pitches`.`pitchType` = '{$pitchT[$i]}' AND `srjc_game-pitches`.`firstpitch` =  '1' {$option1} {$option2} THEN 1 ELSE 0 END) as 'allbatFB'";
                    if($i != (count($pitchT) - 1)){
                      $allData = $allData.",";
                    }
                      
                    }
                    
                    $allData = $allData."FROM `srjc_game-pitches`
                    INNER JOIN `srjc_game-pitchers` ON `srjc_game-pitches`.`fk_pitchers_id` = `srjc_game-pitchers`.`pitchers_id`
                    WHERE `srjc_game-pitchers`.`pitcherName` = '{$pitcherName}' {$dateOption}";
                    
                    return $allData;
          } 
        
         function createTable($title,$pitcherName, $Data, $myconn, $getOpponent, $showTable3){              
        try{
          
        
   
        $statment = $myconn -> prepare($Data);
        
        $statment -> execute();
        if(!$getOpponent){
        $statment -> bind_result( $allMinSp, $allMaxSp, $allAvgSp,$allS, $allB,$allBat, $allK, $allW, $allH,
                                  $allSFB, $allBFB, $allMinSpFB,$allMaxSpFB, $allAvgSpFB,$allBatFB, 
                                  $allSCB, $allBCB, $allMinSpCB,$allMaxSpCB, $allAvgSpCB,$allBatCB, 
                                  $allSCH, $allBCH, $allMinSpCH,$allMaxSpCH, $allAvgSpCH,$allBatCH,  
                                  $allSSL, $allBSL, $allMinSpSL,$allMaxSpSL, $allAvgSpSL,$allBatSL, 
                                  $allSOT, $allBOT, $allMinSpOT,$allMaxSpOT, $allAvgSpOT,$allBatOT);
        }else{
            $statment -> bind_result( $opponent, $allMinSp, $allMaxSp, $allAvgSp,$allS, $allB,$allBat, $allK, $allW, $allH,
                                      $allSFB, $allBFB, $allMinSpFB,$allMaxSpFB, $allAvgSpFB,$allBatFB,
                                      $allSCB, $allBCB, $allMinSpCB,$allMaxSpCB, $allAvgSpCB,$allBatCB,
                                      $allSCH, $allBCH, $allMinSpCH,$allMaxSpCH, $allAvgSpCH,$allBatCH,
                                      $allSSL, $allBSL, $allMinSpSL,$allMaxSpSL, $allAvgSpSL,$allBatSL,
                                      $allSOT, $allBOT, $allMinSpOT,$allMaxSpOT, $allAvgSpOT,$allBatOT);
        }
                                  
        }catch(Exception $e){
          echo $e;
        }
        
                                 
                               
                                 
       
       
      // echo "<div class='card'>
      //         <h3 class='card-header'>
      //           All Pitches
      //         </h3>
      //         <div class='card-body'>";
              
          
          
        while($statment -> fetch()){
             $tableCollection = array(new TableData("All",$allS, $allB,$allMinSp, $allMaxSp, $allAvgSp,$allBat),
                                 new TableData("FB", $allSFB, $allBFB, $allMinSpFB,$allMaxSpFB, $allAvgSpFB,$allBatFB),
                                 new TableData("CB", $allSCB, $allBCB, $allMinSpCB,$allMaxSpCB, $allAvgSpCB,$allBatCB),
                                 new TableData("CH", $allSCH, $allBCH, $allMinSpCH,$allMaxSpCH, $allAvgSpCH,$allBatCH),
                                 new TableData( "SL",$allSSL, $allBSL, $allMinSpSL,$allMaxSpSL, $allAvgSpSL,$allBatSL),
                                 new TableData("OT",$allSOT, $allBOT, $allMinSpOT,$allMaxSpOT, $allAvgSpOT,$allBatOT));
    if(!$getOpponent){
              echo "<div class='col-sm-12'><div class='pitcher-name'>{$pitcherName}</div> <div class='title'>{$title}</div></div>";
              }else{
              echo "<div class='col-sm-12'><div class='pitcher-name'>{$pitcherName}</div> <div class='game-title'>{$title} {$opponent}</div></div>";
            
              }
                          if($showTable3){
          echo "<div class='my-tables row'><table class='game-stat table table-striped table-border  table-sm'>
    <thead>
       <tr>
        <th scope='col'>Hitters</th>
        <th scope='col'>Strike Outs</th>
        <th scope='col'>Walks</th>
        <th scope='col'>Hits</th>
        
    </tr>
  </thead>
  <tbody>";
       
                 echo  " <tr>
               <td>$allBat</td>
               <td>$allK</td>
               <td>$allW</td>
               <td>$allH</td>
          </tr>
          
          </tbody></table></div>";
          }

                       echo  "<div class='row'>
                <div class='com-md-1'>
                <table class='table table-striped table-sm key'>
                <tbody>
                <tr>
                 <th scope='row'>{$tableCollection[0] -> title}</th>
                </tr>
                <tr>
                 <th scope='row'>{$tableCollection[1] -> title}</th>
                </tr>
                <tr>
                 <th scope='row'>{$tableCollection[2] -> title}</th>
                </tr>
                <tr>
                 <th scope='row'>{$tableCollection[3] -> title}</th>
                </tr>
                <tr>
                 <th scope='row'>{$tableCollection[4] -> title}</th>
                </tr>
                 <tr>
                 <th scope='row'>{$tableCollection[5] -> title}</th>
                </tr>
                </tbody>
                </table>
                </div>       
                       
  <div class='my-tables col-md-6'><table class='table table-striped table-border table-sm'>
    <thead>
       <tr>
     
      <th scope='col'></th>
      <th scope='col' colspan='3'>Pitch Strike %</th>
    </tr>
    <tr>
      <th scope='col'>PITCHES</th>
      <th scope='col'>STRIKES</th>
      <th scope='col'>BALLS</th>
      <th scope='col'>STRIKE %</th>
  </tr>
  </thead>
  <tbody>";
               for($i = 0;$i <=5;$i++ ){
                 echo  "<tr>
               <td>{$tableCollection[$i] -> total}</td>
               <td>{$tableCollection[$i] -> strikes}</td>
               <td>{$tableCollection[$i] -> balls}</td>
               <td>{$tableCollection[$i] -> strikePerc}</td>
          </tr>";
          }
          echo "</tbody></table></div><div class='my-tables col-md-5'>
          <table class='table table-striped table-border table-sm'>
   <thead>
      <tr>
      <th scope='col'></th>
      <th scope='col' colspan='3'>Pitch Velocity</th>
      </tr>
      <tr>
      <th scope='col'>MIN</th>
      <th scope='col'>MAX</th>
      <th scope='col'>AVE</th>
      </tr>
  </thead>
  <tbody>";
               for($i =0 ;$i <=5;$i++ ){
                 echo  "
                 <tr>
               <td>{$tableCollection[$i] -> minSp}</td>
               <td>{$tableCollection[$i] -> maxSp}</td>
               <td>{$tableCollection[$i] -> avgSp}</td>
          </tr>";
           
          }

          echo "</tbody></table></div>";

          echo "</div>";
        }
        
        $statment -> close();
        
        
        // echo "</div></div>";
      }
      
      $datesArray = array();
        
        $allData = "SELECT `date`
                    FROM `srjc_game-pitchers`
                    WHERE `pitcherName` = '{$pitcherName}'";
        $statment = $myconn -> prepare($allData);
        
        $statment -> execute();
        
        $statment -> bind_result($date);
        
        while($statment -> fetch()){
          
          array_push($datesArray, $date);
        
          }
          
          $statment -> close();

$coordArray = array();

$arrlength = count($datesArray);

for($x = 0; $x < $arrlength; $x++) {
$coordSQL = "SELECT `srjc_game-pitches`.`xCoord`,`srjc_game-pitches`.`yCoord`,`srjc_game-pitches`.`pitchType`   
                    FROM `srjc_game-pitches`
                    INNER JOIN `srjc_game-pitchers` ON `srjc_game-pitches`.`fk_pitchers_id` = `srjc_game-pitchers`.`pitchers_id`
                    WHERE `srjc_game-pitchers`.`pitcherName` = '{$pitcherName}' AND `srjc_game-pitchers`.`date` = '{$datesArray[$x]}'";
$statement = $myconn->prepare($coordSQL);
$statement->execute();
$statement -> bind_result($xCoord, $yCoord, $pType);
$tempArray = array();
while($statement -> fetch()){
  array_push($tempArray, new Coords($xCoord, $yCoord,$pType));
}
array_push($coordArray, $tempArray);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Main Page</title>
                        <!-- Latest compiled and minified CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/menu-style.css">
    <script src="../js/plotPoints.js"></script>
    <script src="../js/mainScript.js"></script>
    </head>
    <body>
        <div id="left-nav-menu" class="clear-header">
        <div class="left-menu-item" id="edit-roster">Edit Roster<i class="fas fa-baseball-ball"></i></div>
        <div class="left-menu-item" id="pitch-tracker">Pitch Tracker<i class="fas fa-baseball-ball"></i></div>
    </div>
      
      <div class="container-fluid main">
        <div class="row" id="header-title" style="font-size:30px;cursor:pointer"><div id='menu-btn'>&#9776;</div><button id="logout" type="button" class="btn btn-default"><a href="logout.php">Logout</a></button></div>
            <div class="row" id="header">
              
      <img id="logo" class="col-sm-2" src="../../img/bearcubs.png" alt="Santa Rosa Bear Cubs Logo">
                <div class="col-md-8 header-title"> <div>Santa Rosa Jr College</div>
                <p>Cubs Pitch Data</p></div>
                
                
                
            </div>
        <div class="row">
          <div class="card col-sm-12 menu">
  <div class="card-body">
    <div class="card-text">
      
      <form method="post" action="main.php">
         <div class="form-group">
    <label for="pitcherSelect">Select Pitcher</label>
    <select class="form-control" id="pitcherSelect" name="pitchername">
    	<?php
    	   $sql     = "SELECT `pitcher_name`, `year`, `season` 
							FROM `srjc_pitcher-roster` 
							ORDER BY `year` DESC";
		$statement = $myconn -> prepare($sql);
		$statement -> execute();
		$statement -> bind_result($Name, $year, $season);
		
    	if($pitcherName == ""){
    	  ?>
    	    <option disabled selected><i>Select Pitcher</i></option>
    	    <?php
    	}
    	while($statement->fetch()){
    		
    		if($Name === $pitcherName){
    	    ?>
    	    <option selected><?php echo $Name; ?></option>
    	    <?php
    		}else{
    		?>
    		<option><?php echo $Name; ?></option>
    		<?php
    		}
         
    		
    	}
    	$statement->close();
    	
    	
    ?>
    </select>
  </div>

   <button type="submit" class="btn btn-primary center-block">Submit</button>
</form>
      
      
      
    </div>
  </div>
</div>
</div>
          
        <?php
        echo "<h1 id='pitcher-title'>{$pitcherName}</h3>";
        
                    
       echo "<div class='row table-group'><div class='col-md-12'>";  
                    $statement = createStatement($pitcherName,"","","",false);
                    createTable("All Pitches",$pitcherName, $statement, $myconn,false, true);
                   echo "</div></div>"; 
       echo "<div class='row table-group'><div class='col-md-12'>";
       $statement = createStatement($pitcherName,"`srjc_game-pitches`.`firstpitch` =  '1'","","",false);
                    createTable("First Pitches",$pitcherName,$statement, $myconn, false, false);
          echo "</div></div>";
           echo "<div class='row table-group'><div class='col-md-12'>";
       $statement = createStatement($pitcherName,"`srjc_game-pitches`.`batterhandness` =  'Right'","","",false);
                    createTable("Right Handed Hitters",$pitcherName,$statement, $myconn,false, true);
          echo "</div></div>";
            echo "<div class='row table-group'><div class='col-md-12'>";
       $statement = createStatement($pitcherName,"`srjc_game-pitches`.`batterhandness` =  'Left'","","",false);
                    createTable("Left Handed Hitters",$pitcherName,$statement, $myconn,false, true);
          echo "</div></div>";
 for($x = 0; $x < $arrlength; $x++) {
        echo "<div class='row table-group'><div class='col-md-7'>";
          $statement = createStatement($pitcherName,"","",$datesArray[$x],true);
                    createTable("{$datesArray[$x]} ",$pitcherName,$statement, $myconn,true, true);
          ?>
         </div>
      
            <div class="chart-data col-md-5">
                      <div class="chart-key">
                <table class='table table-striped table-border table-sm key2'>
                  <thead>
                    <tr>
                      <th scope="row">Key</th>
                    </tr>
                  </thead>
                <tbody>
                <tr>
                 <th scope='row'><div class ="legend-item" style="background-color:<?php echo $colors[0]?>;">FB</div></th>
                </tr>
                <tr>
                  <th scope='row'><div class ="legend-item" style="background-color:<?php echo $colors[1]?>;">CB</div></th>
                </tr>
                <tr>
                <th scope='row'><div class ="legend-item" style="background-color:<?php echo $colors[2]?>;">CH</div></th>
                </tr>
                <tr>
                  <th scope='row'><div class ="legend-item" style="background-color:<?php echo $colors[3]?>;">SL</div></th>
                </tr>
                <tr>
                <th scope='row'><div class ="legend-item" style="background-color:<?php echo $colors[4]?>;">OT</div></th>
                </tr>
                </tbody>
                </table>
                </div>  
          <svg class="cust-svg" width="432px" height="473px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <g id="mySVG<?php echo $x ?>">
  <rect id="rect"  x="2%" y="0" width="96%" height="100%"
  style="fill:white;stroke:rgba(0,0,0,0);stroke-width:2;fill-opacity:0.9;stroke-opacity:0.9" />
    <rect x="25%" y="20%" width="50%" height="60%"
  style="fill:white;stroke:black;stroke-width:3;fill-opacity:0.1;stroke-opacity:0.9" />
  <line x1="50%" y1="20%" x2="50%" y2="80%" style="stroke:black;stroke-width:3" />
  <line x1="25%" y1="40%" x2="75%" y2="40%" style="stroke:black;stroke-width:3" />
</g>
</svg>
        </div>
        <script>
          plotPoints(<?php echo json_encode($coordArray[$x]);?>,<?php echo json_encode($colors); ?>, <?php echo $x; ?> );
        </script>
          
          <?php
        echo "</div>";
    
          }    
        echo "</div>";
      
        include "SQLDisconnect.inc.php";
        ?>
     
    </body>
</html>