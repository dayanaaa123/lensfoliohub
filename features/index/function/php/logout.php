<?php
  session_start();

  $_SESSION = array();

  session_destroy();

  header('Location: ../../../../authentication/web/api/login.php');
  exit;
?>