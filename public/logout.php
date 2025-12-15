<?php
// public/logout.php
require_once '../bootstrap.php';
require_once '../src/Auth.php';

Auth::logout();

// Redirect back to the login page
header("Location: index.php");
exit;