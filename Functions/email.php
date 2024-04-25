<?php
function email($subject, $url, $body)
{
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.ethereal.email';
    $mail->SMTPAuth = true;
    $mail->Username = 'virginia91@ethereal.email';
    $mail->Password = 'VjQ1fE6EyXT6VhaEAR';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->From = "noreply@stefanssupershop.com";
    $mail->FromName = "Stefans Supershop";
    $mail->addAddress($_POST['username']);
    // $mail->addReplyTo("info@chefschoice.com", "No-Reply");
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->send();
}

?>