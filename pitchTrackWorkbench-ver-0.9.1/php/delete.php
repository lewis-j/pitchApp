<?php
    session_start();
    include "SQLConnect.inc.php";
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Delete</title>
    </head>
    <body>
        <h1>You are logged into the Mini CMS</h1>
        <!-- delete row with matching ID from table -->
        <?php
        // make sure an id passed in querystring ?id=#
        if ( isset($_GET["id"]) ) {
            // Define SQL statement
            $mysql = "DELETE FROM `srjc_pitcher-roster`
                      WHERE `pitcher_id` = ?";
    
            // send templated text of my SQL command to MySQL
            $mystatement = $myconn -> prepare( $mysql );
    
            // Align the parameters with variables
            $mystatement -> bind_param('i', $_GET["id"]);
            
            // tell MySQL to perform the SQL command
            $mystatement -> execute();
            
            //print_r($mystatement);
            
            // check to see if any rows affected
            if ($mystatement -> affected_rows > 0) {
                print "<p>Row deleted</p>";
            } else {
                print "<p>No rows affected</p>";
            }
            
            // close statement
            $mystatement -> close();
        }
        print "<p><a href='main-list.php'>List</a></p>";
        ?>
    </body>
</html>
<?php
    include "SQLDisconnect.inc.php";
?>