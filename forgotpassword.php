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
$username = "";
$password = "";
$passwordAgain = "";
$message = "";
$resetOk = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];

    try {
        $Username = $_ENV['Username'];
        $Password = $_ENV['Password'];

        $dbContext->getUsersDatabase()->getAuth()->forgotPassword($_POST['username'], function ($selector, $token) use ($Username, $Password) {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.ethereal.email';
            $mail->SMTPAuth = true;
            $mail->Username = $Username;
            $mail->Password = $Password;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->From = "noreply@stefanssupershop.com";
            $mail->FromName = "Stefans Supershop";
            $mail->addAddress($_POST['username']);
            $mail->isHTML(true);
            $mail->Subject = "Stefans Supershop - reset password";
            $url = 'http://localhost:8000/resetPassword.php?selector=' . \urlencode($selector) . '&token=' . \urlencode($token)
            ;
            $mail->Body = "
            <h2>Password reset</h2>
            <p>If you've lost your password or wish to reset it, click here: <a href='$url'>$url'></a> to get started</p>";
            $mail->send();
        });

        $resetOk = true;
    } catch (\Delight\Auth\InvalidEmailException $e) {
        $message = "Invalid email address";
    } catch (\Delight\Auth\EmailNotVerifiedException $e) {
        $message = "Email not verified";
    } catch (\Delight\Auth\ResetDisabledException $e) {
        $message = "Password reset is disabled";
    } catch (\Delight\Auth\TooManyRequestsException $e) {
        $message = "Too many requests";
    } catch (\Exception $e) {
        $message = "Something went wrong";
    }
}

?>

<body>
    <main>
        <?php if ($resetOk) {
            ?>
            <div class="forgotpassword__wrapper">
                <p class="forgot__email-text">Please check your email inbox for a message from us containing a link to
                    reset your password.</p>
            </div>
            <?php
        } else {
            ?>

            <div class="reset">
                <section class="reset__wrapper">
                    <div class="reset__message__wrapper">
                        <h2>Forgot password:
                            <?php echo $message; ?>
                        </h2>
                    </div>
                    <form method="post" class="form">
                        <div class="username__wrapper">
                            <label for="name">Username</label>
                            <input class="reset__input" type="email" name="username" />
                        </div>
                        <div class="reset__submit__wrapper">
                            <button class="reset__button" type="submit" value="Skicka">
                                Reset password
                            </button>
                        </div>
                    </form>
                    <?php
        }
        ?>
            </section>
        </div>
    </main>

</body>

</html>