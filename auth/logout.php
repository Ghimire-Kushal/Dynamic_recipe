<?php
$__ROOT_BASE = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/'); /*ROOT_BASE*/

if (session_status() === PHP_SESSION_NONE) { session_start(); }
session_unset();
session_destroy();
header('Location: ' . $__ROOT_BASE . '/index.php');
exit;
