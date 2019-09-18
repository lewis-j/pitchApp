<?php
    include "SQLConnect.inc.php";
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Main List (Retrieve)</title>
    </head>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="../styles/style.css">
        <link rel="stylesheet" href="../styles/header.css">
          <script src="../js/menu_btn.js"></script>

    <body>
      <div id="left-nav-menu" class="clear-header">
      <div class="left-menu-item" id="cubs-pitch">Cubs Pitch Data<i class="fas fa-baseball-ball"></i></div>
      <div class="left-menu-item" id="pitch-tracker">Pitch Tracker<i class="fas fa-baseball-ball"></i></div>
  </div>

    <div class="container-fluid main">
      <div class="row" id="header-title" style="font-size:30px;cursor:pointer"><div id='menu-btn'>&#9776;</div><button id="logout" type="button" class="btn btn-default"><a href="logout.php">Logout</a></button></div>
          <div class="row" id="header">

    <img id="logo" class="col-sm-2" src="../../img/bearcubs.png" alt="Santa Rosa Bear Cubs Logo">
              <div class="col-md-8 header-title"> <div>Santa Rosa Jr College</div>
              <p>Roster Editor</p></div>



          </div>
        <?php
        // Define SQL statement
        $mysql = "SELECT `team_id`,`year`, `season`
        FROM `srjc_team_list`";


        // I am sending the templated text of my SELECT command to MySQL
        $mystatement = $myconn -> prepare( $mysql );

        // tell mysql to perform the SQL command with our values
        $mystatement -> execute();

        // Bind results: id, name, address, hours
        $mystatement -> bind_result($id, $year, $season);

        ?>
          <div class="container">
              <div class="row">
        <?php
        while ( $mystatement -> fetch() ) {
            echo "<div class='col-md-12 seasons-info'><a href='main-list.php?id=$id'><h4>$year</h4> <h5>$season</h5></a>
                  <a class='button' href='edit-team.php?id=$id'>Edit</a>
                  <a class='button' href='delete-team.php?id=$id'>Delete</a></div>
                  ";

        }
        echo "<div class='col-md-12 seasons-info'><a href='edit-team.php?id=0'>Add New Season</a></div>";
      ?>

    </div>

</div>
    </body>
</html>
<?php
    include "SQLDisconnect.inc.php";
?>
