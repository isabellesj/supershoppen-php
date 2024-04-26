<?php
ob_start();
require_once ('lib/PageTemplate.php');
require_once ("Utils/Validator.php");

$token = $_GET['token'] ?? "";
$selector = $_GET['selector'] ?? "";


if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Register";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}

$dbContext = new DbContext();
$message = "";

$username = "";
$password = "";
$Name = "";
$StreetAddress = "";
$ZipCode = "";
$City = "";

$v = new Validator($_POST);
$registeredOk = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $Name = $_POST['Name'];
    $StreetAddress = $_POST['StreetAddress'];
    $City = $_POST['City'];
    $ZipCode = $_POST['ZipCode'];

    $v->field('Name')->required()->alpha([' '])->min_len(1)->max_len(200);
    $v->field('username')->required()->email();
    $v->field('password')->required()->min_len(8)->max_len(16)->must_contain('@#$&!')->must_contain('a-z')->must_contain('A-Z')->must_contain('0-9');
    $v->field('StreetAddress')->required();
    $v->field('ZipCode')->required();
    $v->field('City')->required();

    if ($v->is_valid()) {
        try {
            $userId = $dbContext->getUsersDatabase()->getAuth()->register($username, $password, $username, function ($selector, $token) use ($dbContext, $username, $Name, $StreetAddress, $City, $ZipCode) {

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.ethereal.email';
                $mail->SMTPAuth = true;
                $mail->Username = 'virginia91@ethereal.email';
                $mail->Password = 'VjQ1fE6EyXT6VhaEAR';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                //det här ska finnas i .env

                $mail->From = "noreply@stefanssupershop.com";
                $mail->FromName = "Stefans Supershop";
                $mail->addAddress($_POST['username']);
                $mail->isHTML(true);
                $mail->Subject = "Stefans Supershop - verify email";
                $url = 'http://localhost:8000/verifyEmail.php?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
                $mail->Body = "
                <h2>Hi!</h2>
                <p>Thank you for signing up. Please verify your email address by clicking the following link: <a href='$url'>$url</a></p>
                <p>If you are having any issues, please don't hesitate to contact us.</p>
                <p>Do you have any problems or need help? <br> 
                Please contact info@stefanssupershop.com</p>";
                $mail->send();
            });

            $registeredOk = true;
            $dbContext->addUser($username, $Name, $StreetAddress, $City, $ZipCode, $username);

        } catch (\Delight\Auth\InvalidEmailException $e) {
            $message = "Incorrect email";
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            $message = "Invalid password";
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            $message = "User already exist";
        } catch (\Exception $e) {
            $message = "Something went wrong";
        }

    } else {
        $message = "Fix registration errors";
    }

}
;


?>
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
                    <?php echo $message; ?>
                    <form method="post">
                        <input class="input" type="email" name="username" value="<?php echo $username ?>"
                            placeholder="Enter Your Email">
                        <br />
                        <br />
                        <input class="input" type="password" name="password" value="<?php echo $password ?>"
                            placeholder="Enter Your Password">
                        <br />
                        <br />
                        <input class="input" type="name" name="Name" value="<?php echo $Name ?>" placeholder="Name">
                        <br />
                        <br />
                        <input class="input" type="street" name="StreetAddress" value="<?php echo $StreetAddress ?>"
                            placeholder="Street address">
                        <br />
                        <br />
                        <input class="input" type="postal" name="ZipCode" value="<?php echo $ZipCode ?>"
                            placeholder="Zip code">
                        <br />
                        <br />
                        <input class="input" type="city" name="City" value="<?php echo $City ?>" placeholder="City">
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