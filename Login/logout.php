<?php
session_start();
session_destroy();
header("Location: /phpcode/project management/INVOICE-DEMO/Login/login.php");
exit();
