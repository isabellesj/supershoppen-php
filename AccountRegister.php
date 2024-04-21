<?php
ob_start();
require_once ('lib/PageTemplate.php');

if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Regsier";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}

$subject = "Verify email";
$url = 'http://localhost:8000/user/verify_email?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
$body = "
    <h2>Hi!</h2>
    <p>Thank you for signing up. Please verify your email address by clicking the following link: <a href='$url'>$url</a></p>
    <p>If you are having any issues, please don't hesitate to contact us.</p>
    <p>Do you have any problems or need help? <br> 
    Please contact info@stefanssupershop.com</p>";

$dbContext = new DbContext();
$message = "";
$username = "";
$registeredOk = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO:Add validation - redan registrerad, password != passwordAgain
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $userId = $dbContext->getUsersDatabase()->getAuth()->register($username, $password, $username, function ($selector, $token) use ($subject, $url, $body) {
            email($subject, $url, $body);
        });
        $registeredOk = true;
    } catch (\Delight\Auth\InvalidEmailException $e) {
        $message = "Incorrect email";
    } catch (\Delight\Auth\InvalidPasswordException $e) {
        $message = "Invalid password";
    } catch (\Delight\Auth\UserAlreadyExistsException $e) {
        $message = "User already exist";
    } catch (\Exception $e) {
        $message = "Something went wrong";
    }
}
?>
<p>
<div class="row">
    <?php if ($registeredOk) {

        ?>
        <div>
            <p>Thank you for registering. Please check your email inbox for a message from
                us containing a verification link to complete your registration process.</p>
        </div>
        <?php
    } else {
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="newsletter">
                    <p>User<strong>&nbsp;REGISTER</strong></p>
                    <form>
                        <input class="input" type="email" placeholder="Enter Your Email">
                        <br />
                        <br />
                        <input class="input" type="password" placeholder="Enter Your Password">
                        <br />
                        <br />
                        <input class="input" type="password" placeholder="Repeat Password">
                        <br />
                        <br />
                        <input class="input" type="name" placeholder="Name">
                        <br />
                        <br />
                        <input class="input" type="street" placeholder="Street address">
                        <br />
                        <br />
                        <input class="input" type="postal" placeholder="Postal code">
                        <br />
                        <br />
                        <input class="input" type="city" placeholder="City">
                        <br />
                        <br />
                        <button class="newsletter-btn"><i class="fa fa-envelope"></i> Register</button>
                    </form>

                </div>
            </div>
        </div>
        <?php
    }
    ?>


</div>


</p>