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
            
            $user = new User();
            $user->email = Helper::post_val("email");
            $user->password = Helper::post_val("password");

            $user->validate_with(["email", "password"]);
            $errors = $user->get_errors();

            if($errors->is_empty()){
                $existing_user = new User();
                $existing_user = $existing_user->where(["email" => $user->email])->one();

                if(!empty($existing_user) && $existing_user->type == USER_TYPE_EMAIL){

                    if(password_verify($user->password, $existing_user->password)) {

                        $image_name = $existing_user->image_name;
                        $existing_user->image_name = null;

                        $response_arr = $existing_user->to_valid_array();

                        $response_arr["image_link"] = IMAGE_LINK . $image_name;
                        $response_arr["thumb_link"] = THUMB_LINK . $image_name;

                        $response->create(200, "Success.", $response_arr);

                    }else $response->create(201, "Invalid Email/Password.", null);
                }else $response->create(201, "Invalid User.", null);

            }else $response->create(201, $errors->get_error_str(), null);
        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>