<?php
    include "SQLConnect.inc.php";
    echo "Welcome to the Santa Rosa Junior College Baseball DBMS";
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

    <body>
        <h1>You are logged into the Santa Rosa Junior College Baseball DBMS</h1>
        <p>Please choose an Action for a row.</p>
        <!-- load all rows from table -->
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
            echo "<div class='col-md-12 seasons-info'><a href='main-list.php?id=$id'><h4>$year</h4> <h5>$season</h5></a></div>";
        }
        echo "<div class='col-md-12 seasons-info'><a href='edit-team.php?id=$id'>Add New Season</a></div>";
      ?>

    </div>

</div>
    </body>
</html>
<?php
    include "SQLDisconnect.inc.php";
?>
