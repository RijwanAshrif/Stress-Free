<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer{
    
    private $message;
    private $email;
    private $smtp_config;
    private $is_message;
    private $subject;

    public function __construct($message, $subject = false, $email, $admin_id, $is_message = false){
		$this->is_message = $is_message;
		$this->message = $message;
		$this->email = $email;
		$this->subject = $subject;
        $smtp_config = new Smtp_Config();
        $this->smtp_config = $smtp_config->where(["admin_id" => $admin_id])->one();
    }

    function send(){
        if(empty($this->smtp_config)) return "Invalid Admin";

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {

            $mail->ClearAllRecipients( );

            //Server settings
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = $this->smtp_config->host;  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $this->smtp_config->username;                 // SMTP username
            $mail->Password = $this->smtp_config->smtp_password;                           // SMTP password
            $mail->SMTPSecure = $this->smtp_config->encryption;                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = $this->smtp_config->port;                                    // TCP port to connect to


            //Recipients
            $mail->setFrom($this->smtp_config->sender_email, 'Mailer');
            $mail->addAddress($this->email);     // Add a recipient
          
            //Content
            $mail->isHTML(true);
            if($this->is_message){

                if($this->subject) $mail->Subject = $this->subject;
                else $mail->Subject = "Reply Of Your Question";
                
                $mail_body = $this->message;
            }else{
                $mail->Subject = 'Verify your account';
                $mail_body = "<h2>Success!!!!</h2>";
                $mail_body .= "<h4>Thanks for registering.</h4>";
                $mail_body .= "<p>Your verification code : <b>" . $this->message . "</b></p>";
                $mail_body .= "<p>Do not share this code with anyone.</p>";
            }

            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->Body = $mail_body;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            return 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;

        }
    }

}