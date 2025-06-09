<?php
    session_start();
    //setcookie("PHPSESSID", "",time() - 7200, true, false);
    session_unset();
    session_destroy();
    //session_write_close();
    header("Location: ../index.php");
    exit();
?>