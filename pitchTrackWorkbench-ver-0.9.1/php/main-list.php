<?php
    include "SQLConnect.inc.php";
    echo "Welcome to the Santa Rosa Junior College Baseball DBMS";
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Main List (Retrieve)</title>
    </head>
    <body>
        <h1>You are logged into the Santa Rosa Junior College Baseball DBMS</h1>
        <p>Please choose an Action for a row.</p>
        <!-- load all rows from table -->
        <?php
        $team_id = $_GET["id"];

        // Define SQL statement
     $mysql = "SELECT `pitcher_id`,`pitcher_name`
          FROM `srjc_pitcher-roster`
          WHERE `team_id`= '{$team_id}'";

        // I am sending the templated text of my SELECT command to MySQL
        $mystatement = $myconn -> prepare( $mysql );

        // tell mysql to perform the SQL command with our values
        $mystatement -> execute();

        // Bind results: id, name, address, hours
        $mystatement -> bind_result($myid, $mypitcher_name );

        // loop thru found rows in results
        print "<table border='1' cellpadding='10' style='border-spacing:10px; border:4px dashed olive'>";
        print "<tr>";
        print "<th>Pitcher Name</th> <th>Action</th>";
        print "</tr>";

        while ( $mystatement -> fetch() ) {
            // after we call fetch(), our bound variables (made with bind_result)
            // contain the column values for the row that we got from fetch()
            print "<tr>";
            print "<td>$mypitcher_name</td>";
            print "<td><a href='edit.php?id=$myid&team_id=$team_id'>edit</a> ";
            print "<a href='delete.php?id=$myid'>delete</a></td>";
            print "</tr>";
        }

        print "</table>";
        print "<p><a href='edit.php?id=0&team_id=$team_id'>Add</a></p>";
        print "<p><a href='logout.php'>Logout</a></p>";
        ?>
    </body>
</html>
<?php
    include "SQLDisconnect.inc.php";
?>
