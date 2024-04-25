<?php
ob_start();

require 'vendor/autoload.php';
require_once ('Models/Database.php');
require_once ('lib/PageTemplate.php');

if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Verify Email";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}

$dbContext = new DbContext();

try {
    $dbContext->getUsersDatabase()->getAuth()->confirmEmail($_GET['selector'], $_GET['token']);
} catch (Exception $e) {
    $message = "Could not login";
} catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
    $message = "Invalid token";
} catch (\Delight\Auth\TokenExpiredException $e) {
    $message = "Token expired";
} catch (\Delight\Auth\UserAlreadyExistsException $e) {
    $message = "Email address already exists";
} catch (\Delight\Auth\TooManyRequestsException $e) {
    $message = "Too many requests";
}

?>
<!DOCTYPE html>
<html lang="en">

<body>
    <main>
        <div>
            <p>Your account is now verified! Click here to log in:</p>
            <button><a href="/AccountLogin.php">Login</a></button>
        </div>


    </main>

</body>

</html>