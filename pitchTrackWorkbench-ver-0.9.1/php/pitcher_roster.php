
<?php
include "custom_error.inc.php";
include "SQLConnect.inc.php";

   $sql     = "SELECT `pitcher_name`, `year`, `season` 
							FROM `srjc_pitcher-roster` 
							ORDER BY `year` DESC";
		$statement = $myconn -> prepare($sql);
		$statement -> execute();
		$statement -> bind_result($pitcherName, $year, $season);
		

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Choose A Pitcher </title>
                <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="style.css">
    </head>
    <body style="background-image: url('homeplate.jpg'); ">
      <div class="container-fluid">
               <div class="row header">
      <img id="logo" class="col-sm-2" src="../img/bearcubs.png" alt="Santa Rosa Bear Cubs Logo">
                <div class="col-md-8 header-title"> <div>Santa Rosa Jr College</div>
                <p>Cubs Pitch Data</p></div>
                
                <div class="col-md-2 text-right" ><button type="button" class="btn btn-default"><a href="logout.php">Logout</a></button></div>
                
            </div>
        
      </div>
<div class="container fluid">
  <div class='row'><div class='col-md-12'>
          <div class="panel panel-default menu">
 
  <div class="panel-body">
      <form method="post" action="main.php">
         <div class="form-group">
    <label for="pitcherSelect">Select a Pitcher</label>
    <select class="form-control" id="pitcherSelect" name="pitchername">
    	<?php
    	while($statement->fetch()){
    		?>
         <option><?php echo $pitcherName; ?></option>
    		<?php	
    	}
    ?>
    </select>
  </div>

   <button type="submit" class="btn btn-primary center-block">Submit</button>
</form>
   </div>
</div>         

</div> 
</div>
</div>
<?php
$statement->close();
  include "SQLDisconnect.inc.php";
        ?>
    </body>
</html>