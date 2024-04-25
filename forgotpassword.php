<?php
require_once ('vendor/autoload.php');
require_once ('Models/Database.php');

$q = $_GET['q'] ?? "";

$dbContext = new DbContext();
$message = "";
$resetOk = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];

    try {
        $expirationTime = 86400;

        $dbContext->getUsersDatabase()->getAuth()->forgotPassword($_POST['username'], function ($selector, $token) use ($expirationTime) {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.ethereal.email';
            $mail->SMTPAuth = true;
            $mail->Username = 'virginia91@ethereal.email';
            $mail->Password = 'VjQ1fE6EyXT6VhaEAR';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            //det här ska finnas i .env

            $mail->From = "noreply@chefschoice.com";
            $mail->FromName = "Chef";
            $mail->addAddress($_POST['username']);
            $mail->addReplyTo("reset@chefschoice.com", "No-Reply");
            $mail->isHTML(true);
            $mail->Subject = "Chef's Choice - reset password";
            $url = 'http://localhost:8000/user/resetPassword?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
            $mail->Body = "
            <h2>Password reset</h2>
            <p>If you've lost your password or wish to reset it, click here: <a href='$url'>$url'></a> to get started</p>";
            $mail->send();
        }, $expirationTime);
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Chef's Choice</title>
    <script src="https://kit.fontawesome.com/2471abcbe0.js" crossorigin="anonymous"></script>
    <link href="/css/style.css" rel="stylesheet" />
</head>

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
                        <a class="cancel__link" href="/user/login"><i class="fa-solid fa-xmark"></i></a>
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