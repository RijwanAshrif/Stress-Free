<?php require_once('../../../private/init.php'); ?>

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = new Response();
$errors = new Errors();

if(Helper::is_post()){
    $admin_token = Helper::post_val("admin_token");
    $user_token = Helper::post_val("user_token");

    if($admin_token && $user_token){
        $admin_token_db = new Setting();
        $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

        $user_token_db = new User_Token();
        $user_token_db = $user_token_db->where(["user_token" => $user_token])->one();

        if(!empty($admin_token_db) && !empty($user_token_db)){
            $user = new User();

            $user->email = Helper::post_val("email");
            $user->validate_with(["email"]);
            $errors = $user->get_errors();

            if($errors->is_empty()){
                $existing_user = new User();
                $existing_user = $existing_user->where(["email" => $user->email])->one();

                if(!empty($existing_user) && $existing_user->type == USER_TYPE_EMAIL){

                    $generated_password = bin2hex(openssl_random_pseudo_bytes(6));
                    $user->password = password_hash($generated_password, PASSWORD_BCRYPT);

                    if($user->where(["id" => $existing_user->id])->update() > 0) {
                        $site_config = new Site_Config();
                        $site_config = $site_config->where(["admin_id" => $admin_token_db->admin_id])->one();

                        $mail_body = main_body($user->email, $generated_password, $site_config->title);
                        $mail_sent = send($mail_body, "User Registration", $site_config->title, $user->email, $admin_token_db->admin_id);

                        if($mail_sent === true){
                            
                            $user_arr = $user->to_valid_array();
                            
                            $response->create(200, "Success.", $user_arr);

                        } else $response->create(201, $mail_sent, null);
                    } else $response->create(201, "Something Went Wrong. Please try Again.", null);

                } else $response->create(201, "Invalid User", null);
            } else $response->create(201, $errors->get_error_str(), null);

        } else $response->create(201, "Invalid Token Found", null);
    } else $response->create(201, "No Token Found", null);
} else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();


function main_body($receiver_email, $password, $company_name){
    $mail_body  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $mail_body .= '<html xmlns="http://www.w3.org/1999/xhtml" lang="en" style="width: 100%; height: 100%;">';

    $mail_body .= '<head>';
    $mail_body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    $mail_body .= '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>';
    $mail_body .= '</head>';
    $mail_body .= '<body style="height: 100%; width: 100%; margin: 0; padding: 0;">';


    $mail_body .= '<div style="width: 100%; height: 100%; padding: 0px; " >';
    $mail_body .= '<div style="width: 700px;">';

    $mail_body .= '<h4 style="font-size: 16px;">This is you new credentials.</h4>';
    $mail_body .= '<h3 style="font-weight: 400; font-size: 2em; line-height: 1; ">Please use this credentials below to login</h3>';

    $mail_body .= '<br>';
    $mail_body .= '<table>';
    $mail_body .= '<tbody>';
    $mail_body .= '<tr>';

    $mail_body .= '<td>';


    $mail_body .= '<p><b>Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </b>' . $receiver_email . '</p>';
    $mail_body .= '<p><b>Password &nbsp;&nbsp;: </b>' . $password . '</p>';

    $mail_body .= '</td>';

    $mail_body .= '</tr>';
    $mail_body .= '</tbody>';
    $mail_body .= '</table>';


    $mail_body .= '<br>';

    $mail_body .= '<p style="font-size: 16px;">If you did not register in ' . $company_name . ', You can safely ignore this email.</p>';
    $mail_body .= '<p style="font-size: 16px;">Someone else might have typed your email by mistake.</p>';
    $mail_body .= '<br>';
    $mail_body .= '<br>';
    $mail_body .= '<p style="font-size: 16px;">Thanks</p>';
    $mail_body .= '<p style="font-size: 16px;">The ' . ucfirst($company_name) . ' Team</p>';

    $mail_body .= '</div>';
    $mail_body .= '</div>';
    $mail_body .= '</body>';
    $mail_body .= '</html>';

    return $mail_body;
}


function send($mail_body, $mail_subject, $company_name, $receiver_email, $admin_id){
    $mail = new PHPMailer(true);
    try {
        $mail->ClearAllRecipients( );

        $mail->isSMTP();

        $smtp_config = new Smtp_Config();
        $smtp_config = $smtp_config->where(["admin_id" => $admin_id])->one();

        $mail->Host = trim($smtp_config->host);
        $mail->SMTPAuth = true;
        $mail->Username = trim($smtp_config->username);
        $mail->Password = trim($smtp_config->smtp_password);
        $mail->SMTPSecure = trim($smtp_config->encryption);
        $mail->Port = trim($smtp_config->port);


        $mail->setFrom(trim($smtp_config->sender_email), $company_name);
        $mail->addAddress($receiver_email);


        $mail->isHTML(true);

        $mail->Subject = $mail_subject;

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

?>