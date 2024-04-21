<?php

ob_start();
require 'vendor/autoload.php';
require_once ('lib/PageTemplate.php');
require_once ("Models/Database.php");

if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Login";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}

$dbContext = new DBContext();

$dbContext->getUsersDatabase()->getAuth()->logOut();
header('Location: /');
exit;