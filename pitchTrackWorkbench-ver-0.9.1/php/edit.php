<?php
    session_start();
    include "SQLConnect.inc.php";
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Edit/Add Form (Retrieve)</title>
        <style>
            label {
                display:inline-block;
                width:120px;
                text-align:left;
            }
        </style>
    </head>
    <body>
        <h1>You are logged into the Santa Rosa Junior College Baseball DBMS</h1>
        <p>Please use the form to edit/add a row.</p>
        <!-- load all rows from table -->
        <?php
        // make sure an id passed in querystring ?id=#
        if ( isset($_GET["id"]) ) {
            // Define SQL statement
            $mysql = "SELECT  `pitcher_id`, `pitcher_name` 
                      FROM  `srjc_pitcher-roster` 
                      WHERE `pitcher_id` = ?";
    
            // send templated text of my SQL command to MySQL
            $mystatement = $myconn -> prepare( $mysql );
    
            // align parameters with our variables
            $mystatement -> bind_param('i', $_GET['id']);
     
            // tell MySQL to perform the SQL command
            $mystatement -> execute();
    
            // Bind results: id, name, address, hours
            $mystatement -> bind_result($myid, $mypitcher_name);
    
            // show found row if any in form
            if ( $mystatement -> fetch() ) {
                // after we call fetch(), our bound variables
                // contain the column values for the row we got
                $mypitcher_name = htmlspecialchars($mypitcher_name);
                $myid = htmlspecialchars($myid);
            } else {
                // if nothing found, prepare empty vars for new item
                $mypitcher_name = '';
                $myid = 0;
            }
            print "<form method='post' action='edit-save.php'>";
            print "<label for='pitcher_name'>Pitcher Name</label>";
            print "<input value='$mypitcher_name' name='pitcher_name' id='pitcher_name'><br>";
            print "<input value='$myid' name='id' type='hidden'><br>";
            print "<input type='submit' value='Save'>";
            print "</form>";
        }
        ?>
    </body>
</html>
<?php
    include "SQLDisconnect.inc.php";
?>