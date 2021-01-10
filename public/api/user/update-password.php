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
            $id = Helper::post_val("id");
            $old_password = Helper::post_val("password");
            $new_password = Helper::post_val("new_password");
            $confirm_password = Helper::post_val("confirm_password");

            if($id && $old_password && $new_password && $confirm_password){

                if($new_password === $confirm_password){
                    $existing_user = new User();
                    $existing_user = $existing_user->where(["id" => $id])->one();

                    if(!empty($existing_user) && $existing_user->type == USER_TYPE_EMAIL){

                        if(password_verify($old_password, $existing_user->password)) {
                            $user = new User();
                            $user->password = password_hash($new_password, PASSWORD_BCRYPT);
                            if($user->where(["id" => $existing_user->id])->update()){

                                $response->create(200, "Success.", $existing_user->to_valid_array());

                            }else $response->create(201, "Something went wrong. Please try again.", null);
                        }else $response->create(201, "Invalid Email/Password.", null);
                    }else $response->create(201, "Invalid User.", null);
                }else $response->create(201, "Two Password didn't match", null);

            } else $response->create(201, "Invalid Parameter", null);

        } else $response->create(201, "Invalid Token Found", null);
    } else $response->create(201, "No Token Found", null);
} else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();


?>