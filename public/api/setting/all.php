<?php require_once('../../../private/init.php'); ?>

<?php

$response = new Response();

if(Helper::is_post()){
    $admin_token = Helper::post_val("admin_token");
    $user_token = Helper::post_val("user_token");

    if($admin_token && $user_token){
        $admin_token_db = new Setting();
        $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

        $user_token_db = new User_Token();
        $user_token_db = $user_token_db->where(["user_token" => $user_token])->one();

        if(!empty($admin_token_db) && !empty($user_token_db)){

            $admob = new Admob();
            $admob = $admob->where(["admin_id"=>$admin_token_db->admin_id])->one();

            $admob->admin_id = null;
            $admob->id = null;

            $response_arr["admob"] = $admob->to_valid_array();
            $response->create(200, "Success", $response_arr);
            
        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>