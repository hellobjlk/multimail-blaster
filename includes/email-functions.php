<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function mmb_send_email_via_phpmailer($smtp_account, $to_email, $subject, $message_body) {
    $mail = new PHPMailer(true); // Set to true to enable exceptions

    try {
        // Set up SMTP settings from the given SMTP account
        $mail->isSMTP();                                     
        $mail->Host = $smtp_account->smtp_host;              
        $mail->SMTPAuth = true;                             
        $mail->Username = $smtp_account->smtp_username;      
        $mail->Password = $smtp_account->smtp_password;      
        $mail->SMTPSecure = $smtp_account->encryption_type;  
        $mail->Port = $smtp_account->smtp_port;             

        // Sender info
        $mail->setFrom($smtp_account->smtp_username, 'Your Name');

        // Recipient
        $mail->addAddress($to_email);

        // Email content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message_body;

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false; // or log the error $mail->ErrorInfo
    }
}
