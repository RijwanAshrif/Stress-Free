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

            $tag_id = Helper::post_val("tag_id");
            if($tag_id){

                $user_id = Helper::post_val("user_id");
                $page = Helper::post_val("page");

                $response_arr = API_Helper::tracks_by_tag($tag_id, $page, $user_id, $admin_token_db->admin_id);
                
                $response->create(200, "Success", $response_arr);

            }else $response->create(201, "Invalid Parameter", null);
        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>