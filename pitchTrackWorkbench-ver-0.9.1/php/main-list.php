<?php
    include "SQLConnect.inc.php";
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Main List (Retrieve)</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="../styles/tables.css">
    <link rel="stylesheet" href="../styles/header.css">
      <script src="../js/menu_btn.js"></script>
    </head>
    <body>
      <div id="left-nav-menu" class="clear-header">
      <div class="left-menu-item" id="cubs-pitch">Cubs Pitch Data<i class="fas fa-baseball-ball"></i></div>
      <div class="left-menu-item" id="pitch-tracker">Pitch Tracker<i class="fas fa-baseball-ball"></i></div>
  </div>
      <div class='container-fluid'>
      <div class="row" id="header-title" style="font-size:30px;cursor:pointer">
        <div id='menu-btn'>&#9776;</div>
        <button id="logout" type="button" class="btn btn-default">
          <a href="logout.php">Logout</a>
        </button>
      </div>
      <div class="row" id="header">

    <img id="logo" class="col-sm-2" src="../../img/bearcubs.png" alt="Santa Rosa Bear Cubs Logo">
              <div class="col-md-8 header-title"> <div>Santa Rosa Jr College</div>

        <?php
        $team_id = $_GET["id"];

        $mysql = "SELECT `year`, `season`
             FROM `srjc_team_list`
             WHERE `team_id`= ?";

             // I am sending the templated text of my SELECT command to MySQL
             $mystatement = $myconn -> prepare( $mysql );

             $mystatement -> bind_param(i, $team_id);
             // tell mysql to perform the SQL command with our values
             $mystatement -> execute();

             // Bind results: id, name, address, hours
             $mystatement -> bind_result($year, $season );

             $mystatement -> execute();

            if( $mystatement -> fetch() ) {
              print"<p>Roster for <b>$season $year</b></p></div>";
              }
              print " </div></div>";

              	$mystatement->close();

              // Define SQL statement
           $mysql = "SELECT `pitcher_id`,`pitcher_name`
                FROM `srjc_pitcher-roster`
                WHERE `team_id`= ?";

        // I am sending the templated text of my SELECT command to MySQL
        $mystatement = $myconn -> prepare( $mysql );

        $mystatement -> bind_param(i, $team_id);

        // tell mysql to perform the SQL command with our values
        $mystatement -> execute();

        // Bind results: id, name, address, hours
        $mystatement -> bind_result($myid, $mypitcher_name );

        // loop thru found rows in results
        print "<div class='container'><div class='row table-group'>


              <div class='my-tables offset-md-2 col-md-8 offset-lg-3 col-lg-6'>
              <table class='game-stat table table-striped table-border'>
             <thead>
             <tr>
             <th scope='col'>Pitcher Name</th>
             <th scope='col'>modify</th>
             </tr>
             </thead><tbody>";

        while ( $mystatement -> fetch() ) {
            // after we call fetch(), our bound variables (made with bind_result)
            // contain the column values for the row that we got from fetch()
            print "<tr data-id='$myid' data-team-id='$team_id' data-name='$mypitcher_name'> ";
            print "<td>$mypitcher_name</td>";
            print "<td><a href='edit.php?id=$myid&team_id=$team_id'>edit</a> ";
            print "<td><button class='edit-player'>edit</a></button> ";
            print "<a href='delete.php?id=$myid'>delete</a></td>";
            print "</tr>";
        }
        $mystatement->close();
        print "<tr>
        <td colspan=2><a href='edit.php?id=0&team_id=$team_id'><button type='button' class='btn btn-info'>Add</button></a></div></div>
        </td>
       </tr>";
        print "</tbody></table></div></div>";
        ?>
        <script src="../js/dynamic_form_item.js"></script>
    </body>
</html>
<?php
    include "SQLDisconnect.inc.php";
?>
