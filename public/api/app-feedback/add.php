<?php require_once('../../../private/init.php'); ?>

<?php

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

            $email = Helper::post_val('email');
            $feedback = Helper::post_val('feedback');
            $hours = 24;

            if($email && $feedback){

                $app_feedback_frm_db = new App_Feedback();
                $app_feedback_frm_db = $app_feedback_frm_db->where(["email" => $email])
                    ->orderBy("created")->orderType("DESC")
                    ->one();

                $enable_updating = true;

                if(!empty($app_feedback_frm_db)){
                    $now = strtotime(date(DATE_FORMAT)); // or your date as well
                    $your_date = strtotime($app_feedback_frm_db->created);
                    $datediff = $now - $your_date;
                    
                    if($datediff < (3600 * $hours)) $enable_updating = false;
                }

                if($enable_updating){
                    $app_feedback = new App_Feedback();
                    $app_feedback->email = $email;
                    $app_feedback->admin_id = $admin_token_db->admin_id;
                    $app_feedback->feedback = $feedback;
                    $app_feedback->created = date(DATE_FORMAT);

                    $app_feedback->id = $app_feedback->save();

                    if($app_feedback->id > 0) $response->create(200, "Success", $app_feedback);
                    else $response->create(201, "Something went wrong. Please try again.", null);
                }else $response->create(201, "You can't post more than one feedback within " . $hours . " hours.", null);


            }else $response->create(201, "Invalid Parameter", null);
                
        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();


?>