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
              $mysql = "DELETE t1  FROM `srjc_game-pitches` as t1
                        JOIN `srjc_game-pitchers` as t2 ON t2.`pitchers_id` = t1.`fk_pitchers_id`
                        JOIN `srjc_pitcher-roster` as t3 ON t3.`pitcher_id` = t2.`pitcher_id`
                        WHERE t3.`team_id` = ?";

            $mystatement = $myconn -> prepare( $mysql );

            $mystatement -> bind_param('i', $_GET["id"]);

            $mystatement -> execute();

            if ($mystatement -> affected_rows > 0) {
                print "<p>".$mystatement -> affected_rows." pitches deleted</p>";
            } else {
                print "<p>No Pitch rows affected</p>";
            }
            $mystatement -> close();


            $mysql = "DELETE t1  FROM `srjc_game-pitchers` as t1
                      JOIN `srjc_pitcher-roster` as t2 ON t2.`pitcher_id` = t1.`pitcher_id`
                      WHERE t2.`team_id` = ?";

          $mystatement = $myconn -> prepare( $mysql );

          $mystatement -> bind_param('i', $_GET["id"]);

          $mystatement -> execute();

          if ($mystatement -> affected_rows > 0) {
              print "<p>".$mystatement -> affected_rows." Games deleted</p>";
          } else {
              print "<p>No Game rows affected</p>";
          }
          $mystatement -> close();

          $mysql = "DELETE t1  FROM `srjc_pitcher-roster` as t1
                    WHERE t1.`team_id` = ?";

        $mystatement = $myconn -> prepare( $mysql );

        $mystatement -> bind_param('i', $_GET["id"]);

        $mystatement -> execute();

        if ($mystatement -> affected_rows > 0) {
            print "<p>".$mystatement -> affected_rows." Players deleted</p>";
        } else {
            print "<p>No Player rows affected</p>";
        }
        $mystatement -> close();

        $mysql = "DELETE t1  FROM `srjc_team_list` as t1
                  WHERE t1.`team_id` = ?";

      $mystatement = $myconn -> prepare( $mysql );

      $mystatement -> bind_param('i', $_GET["id"]);

      $mystatement -> execute();

      if ($mystatement -> affected_rows > 0) {
          print "<p>".$mystatement -> affected_rows." Teams deleted</p>";
      } else {
          print "<p>No Team rows affected</p>";
      }
      $mystatement -> close();
        }
        print "<p><a href='season_selection_menu.php'>List</a></p>";
        ?>
    </body>
</html>
<?php
    include "SQLDisconnect.inc.php";
?>
