<?php
include __DIR__ . "/../includes/auth.php";

session_unset();
session_destroy();

header("Location: /bus/pages/login.php");
exit();


