<?php require_once('../../../private/init.php'); ?>

<?php

$response = new Response();
$errors = new Errors();

if(Helper::is_post()){
    $admin_token = Helper::post_val("admin_token");
    $user_token = Helper::post_val("user_token");
    $user_id = Helper::post_val("user_id");
    $page = Helper::post_val("page");
    $featured_page = Helper::post_val("featured_page");

    $response = API_Helper::main($admin_token, $user_token, $user_id, $page, $featured_page);

}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>