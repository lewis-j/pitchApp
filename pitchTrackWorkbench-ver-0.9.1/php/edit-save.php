<?php
    session_start();
    include "SQLConnect.inc.php";
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Edit Save/Add (Update/Insert)</title>
    </head>
    <body>
        <h1>You are logged into the Santa Rosa Junior College Baseball DBMS</h1>
        <!-- delete row with matching ID from table -->
        <?php

        $teamID = $_POST["team_id"];
        // was form submitted with id field
        if ( isset($_POST["id"]) ) {
            // do we have id to update existing row
            if ( $_POST["id"] <> "0" ) {
                // Define SQL statement
                $mysql = "UPDATE `srjc_pitcher-roster`
                          SET `pitcher_name` = ?,  `team_id` = ?
                          WHERE `pitcher_id` = ?";

                // send templated text of SQL command to MySQL
                $mystatement = $myconn -> prepare( $mysql );

                // Align the parameters with variables
                $mystatement -> bind_param(
                    'sii',
                    $_POST['pitcher_name'],
                    $_POST['team_id'],
                    $_POST["id"]
                );
            }
            // id was zero, so insert new row
            else {
                // Define SQL statement
                $mysql = "INSERT INTO `srjc_pitcher-roster`
                          (`pitcher_name`, `pitcher_id`, `team_id`)
                          VALUES (?, ?, ?)";

                // send templated text of SQL command to MySQL
                $mystatement = $myconn -> prepare( $mysql );

                // Align the parameters with variables
                $mystatement -> bind_param(
                    'sii',
                    $_POST['pitcher_name'],
                    $_POST["id"],
                    $teamID
                );

            }

            // tell MySQL to perform the SQL command
            $mystatement -> execute();

            // print_r($mystatement);

            // check to see if any rows affected by query
            if ($mystatement -> affected_rows > 0) {
                print "<p>Row updated</p>";
            } else {
                print "<p>No rows affected</p>";
            }

            // close statement
            $mystatement -> close();
        }
        header("Location: ./main-list.php?id=$teamID");
        ?>
    </body>
</html>
<?php
    include "SQLDisconnect.inc.php";
?>
