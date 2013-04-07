<?php
session_start();
unset($_SESSION['RoleId']);
unset($_SESSION['IsApproved']);

unset($_SESSION["UserId"] );
unset($_SESSION['RealName']);
unset($_SESSION['RoleId']);
unset($_SESSION['WordCategory']);
unset($_SESSION['HasPackage']);

echo '<script type="text/javascript">location = "login.htm";</script>';
?>