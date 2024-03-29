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
          $team_id = $_GET['id'];
            // Define SQL statement
            $mysql = "SELECT  `year`, `season`
                      FROM  `srjc_team_list`
                      WHERE `team_id` = ?";

            // send templated text of my SQL command to MySQL
            $mystatement = $myconn -> prepare( $mysql );

            // align parameters with our variables
            $mystatement -> bind_param('i', $team_id);

            // tell MySQL to perform the SQL command
            $mystatement -> execute();

            // Bind results: id, name, address, hours
            $mystatement -> bind_result($year, $season);

            // show found row if any in form
            if ( $mystatement -> fetch() ) {
                // after we call fetch(), our bound variables
                // contain the column values for the row we got
                $year = htmlspecialchars($year);
                $season = htmlspecialchars($season);
                $isfetched = true;
                echo "<p> IS TRUE</p>";
            } else {
                $isfetched = false;
                // if nothing found, prepare empty vars for new item
                $year = '';
            }
            print "<form method='post' action='edit-team-save.php'>";
                $today = getdate();
                $currentYear = $today['year'];
                $currentYear = intval($currentYear);
                if($isfetched){
                    print "<select name='season'>";
                       if($season == "Fall"){
                         print
                         "<option value='Fall' selected>Fall</option>
                         <option value='Spring'>Spring</option>
                         </select>";
                       }else{
                         print
                         "<option value='Fall'>Fall</option>
                         <option value='Spring' selected>Spring</option>
                         </select>";
                       }
                         print "<select name='year'>";
                       for($i= $year-3; $i<=$currentYear+2; $i++){
                             if($i == $year){
                               print "<option value='$i' selected> $i </option>";
                             }else {
                               print "<option value='$i'> $i </option>";
                             }
                           }
                       print "</select>
                              <input value=$team_id name='id' type='hidden'><br>";
                       print "<input type='submit' value='Save'>";
                       print "</form>";


                }else{
                  print "<select name='season'>
                        <option value='Fall'>Fall</option>
                        <option value='Spring'>Spring</option>
                        </select>";

                        print "<select name='year'>";
                  for($i=$currentYear; $i<= $currentYear + 3; $i++){
                          print "<option value='$i'> $i </option>";
                        }
                  print "</select>
                         <input value=$team_id name='id' type='hidden'><br>";
                  print "<input type='submit' value='Save'>";
                  print "</form>";

                }

        }
        ?>
    </body>
</html>
<?php
    include "SQLDisconnect.inc.php";
?>
