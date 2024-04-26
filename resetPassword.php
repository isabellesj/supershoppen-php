<?php
ob_start();
require 'vendor/autoload.php';
require_once ('lib/PageTemplate.php');
require_once ('Models/Database.php');
require_once ("Utils/Validator.php");

if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Register";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}


$dbContext = new DbContext();
$v = new Validator($_POST);
$message = "";
$username = "";
$password = "";
$passwordAgain = "";
$resetOk = false;

try {
    $dbContext->getUsersDatabase()->getAuth()->canResetPasswordOrThrow($_GET['selector'], $_GET['token']);
} catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
    $message = "Invalid token";
} catch (\Delight\Auth\TokenExpiredException $e) {
    $message = "Token expired";
} catch (\Delight\Auth\ResetDisabledException $e) {
    $message = "Password reset is disabled";
} catch (\Delight\Auth\TooManyRequestsException $e) {
    $message = "Too many requests";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordAgain = $_POST['passwordAgain'];

    $v->field('password')->required()->min_len(8)->max_len(16)->must_contain('@#$&!')->must_contain('a-z')->must_contain('A-Z')->must_contain('0-9');
    $v->field('passwordAgain')->required()->equals($_POST['password']);

    if ($_POST['password'] == $_POST['passwordAgain']) {
        if ($v->is_valid()) {
            try {
                $dbContext->getUsersDatabase()->getAuth()->resetPassword($_POST['selector'], $_POST['token'], $_POST['password']);
                $resetOk = true;
            } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
                $message = "Invalid token";
            } catch (\Delight\Auth\TokenExpiredException $e) {
                $message = "Token expired";
            } catch (\Delight\Auth\ResetDisabledException $e) {
                $message = "Password reset is disabled";
            } catch (\Delight\Auth\InvalidPasswordException $e) {
                $message = "Invalid password";
            } catch (\Delight\Auth\TooManyRequestsException $e) {
                $message = "Too many requests";
            }
        } else {
            $message = "Fix errors";
        }
    } else {
        $message = "Password does not match";
    }
}
;

?>

<body>
    <main>
        <?php if ($resetOk) {

            ?>
            <div class="forgotpassword__wrapper">
                <p>Your password is now reset. Click here to log in:</p>
                <button class="login__button"><a class="login__link" href="/AccountLogin.php">Login</a></button>
            </div>

            <?php
        } else {
            ?>

            <div>
                <section>
                    <div>
                        <h2>Change password:
                            <?php echo $message; ?>
                        </h2>
                    </div>
                    <form method="post" class="form">
                        <input type="hidden" name="selector" value="<?php echo $_GET['selector']; ?>">
                        <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                        <div>
                            <label for="name">Username</label>
                            <input class="reset__input" type="text" value="<?php echo $username ?>" name="username">
                        </div>
                        <div class="password__wrapper">
                            <label for="name">New password</label>
                            <input class="register__input" type="password" value="<?php echo $password ?>" name="password">
                        </div>
                        <div class="password__wrapper">
                            <label for="name">New password again</label>
                            <input class="register__input" type="password" value="<?php echo $passwordAgain ?>"
                                name="passwordAgain">
                        </div>
                        <div>
                            <button type="submit" value="Reset">
                                Change password
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