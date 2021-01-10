<?php require_once('../../../private/init.php'); ?>

<?php

$response = new Response();
$errors = new Errors();

if(Helper::is_post()){
    $admin_token = Helper::post_val("admin_token");
    $user_token = Helper::post_val("user_token");
    $user_id = Helper::post_val("user_id");
    $page = Helper::post_val("page");

    $response = API_Helper::track_all($admin_token, $user_token, $user_id, $page);
    
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>