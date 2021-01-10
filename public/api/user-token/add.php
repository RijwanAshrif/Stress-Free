<?php require_once('../../../private/init.php'); ?>

<?php
$response = new Response();
$errors = new Errors();

if(Helper::is_post()){
    $admin_token = Helper::post_val("admin_token");
    $ip_address = Helper::post_val("ip_address");
    $username = Helper::post_val("username");
    $user_secret = Helper::post_val("user_secret");

    if($admin_token){
        $setting = new Setting();
        $setting = $setting->where(["admin_token" => $admin_token])->one();

        if(!empty($setting)){
            if($ip_address && $username && $user_secret){
                if($username == API_USERNAME && $user_secret == API_USER_SECRET){
                    $token = bin2hex(openssl_random_pseudo_bytes(15));

                    $user_token = new User_Token();
                    $user_token->user_token = $token;

                    $user_token_from_db = new User_Token();
                    $user_token_from_db = $user_token_from_db->where(["ip_address" => $ip_address])->one();

                    if(!empty($user_token_from_db)){
                        $user_token->created = date(DATE_FORMAT);;
                        $user_token->where(["ip_address" => $ip_address])->update();
                    }else{
                        $user_token->ip_address = $ip_address;
                        $user_token->save();
                    }

                    $response->create(200, "Success", $token);

                }else $response->create(201, "Unauthorized User", null);
            }else $response->create(201, "Invalid Parameter", null);
        }else $response->create(201, "Invalid Token", null);
    }else $response->create(201, "No Admin Token", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>