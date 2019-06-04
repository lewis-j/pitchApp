<?php
    include "custom_error.inc.php";
    include "SQLConnect.inc.php";
    session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Logout</title>
    </head>
    <body>
        <h1>You are now logged out!</h1>
        <button><a href='../login.html'>Login</a></button>
    </body>
</html>
<?php
    include "SQLDisconnect.inc.php";
?>