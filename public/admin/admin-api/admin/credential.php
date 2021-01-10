<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $admin_profile = new Admin();
        $admin_profile = $admin->where(["id" => $admin->id])->one("id, username, email");

        $response_obj["admin"]["text"] = $admin_profile->to_valid_array();
        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){

        $admin_profile = new Admin();
        $id = Helper::post_val("id");
        $admin_profile->username = Helper::post_val("username");
        $admin_profile->email = Helper::post_val("email");
        $password = Helper::post_val("password");

        $admin_from_db = new Admin();
        $admin_from_db = $admin_from_db->where(["id" => $id])->one();
        
        if(password_verify($password, $admin_from_db->password)){
            if($admin_profile->where(["id" => $id])->update()){

                $response_obj["admin"]["text"] = $admin_profile->to_valid_array();
                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Something went wrong.", null);
        }else $response->create(201, "Verification Failed", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>