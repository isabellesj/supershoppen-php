<?php

ob_start();

require 'vendor/autoload.php';
require_once ('Models/Database.php');
require_once ('lib/PageTemplate.php');

if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Login";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}

$dbContext = new DbContext();
$message = "";
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $timestamp = date("Y-m-d H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'];

    try {
        $dbContext->getUsersDatabase()->getAuth()
            ->login($username, $password);
        $userId = $dbContext->getUsersDatabase()->getAuth()->getUserId();
        $dbContext->addLoginSession($userId, $timestamp, $ip);
        header('Location: /');
        exit;
    } catch (Exception $e) {
        $message = "Could not login";
    }
}

?>

<body>
    <?php
    echo $dbContext->getUsersDatabase()->getAuth()->isLoggedIn();
    ?>

    <div class="row">

        <div class="row">
            <div class="col-md-12">
                <div class="newsletter">
                    <p>User<strong>&nbsp;LOGIN</strong></p>
                    <?php echo " $message"; ?>
                    <form method="post" class="form">
                        <input class="input" type="email" name="username" value="<?php echo $username ?>"
                            placeholder="Enter Your Email">
                        <br />
                        <br />
                        <input class="input" type="password" name="password" placeholder="Enter Your Password">
                        <br />
                        <br />
                        <button type="submit" class="newsletter-btn"><i class="fa fa-envelope"></i>Login</button>
                    </form>
                    <a href="/forgotPassword.php">Lost password?</a>
                </div>
            </div>
        </div>


    </div>
</body>