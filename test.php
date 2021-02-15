<?php
    session_start();

    $_SESSION['left']-=1;

    echo $_SESSION['left'];

   // unset($_SESSION['left']);
?>