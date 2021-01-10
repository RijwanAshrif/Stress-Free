<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $admin_profile = new Admin();
        $admin_profile = $admin->where(["id" => $admin->id])->one("id");

        $response_obj["admin"]["text"] = $admin_profile->to_valid_array();
        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){
        $admin_profile = new Admin();
        $admin_profile->password = Helper::post_val("new_password");

        $id = Helper::post_val("id");
        $old_password = Helper::post_val("old_password");
        $confirm_password = Helper::post_val("confirm_password");

        if($confirm_password == $admin_profile->password){

            $validate_password = Helper::invalid_length("Password", $confirm_password, 6);

            if($validate_password == null){
                $admin_from_db = new Admin();
                $admin_from_db = $admin_from_db->where(["id" => $id])->one();

                if(password_verify($old_password, $admin_from_db->password)){
					
					$admin_profile->password = password_hash($confirm_password, PASSWORD_BCRYPT);
					
                    if($admin_profile->where(["id" => $id])->update()){

                        $response_obj["admin"]["text"] = $admin_profile->to_valid_array();
                        $response->create(200, "Success", $response_obj);

                    }else $response->create(201, "Something went wrong.", null);
                }else $response->create(201, "Verification Failed", null);
            }else $response->create(201, $validate_password, null);

        }else $response->create(201, "Password didn't Match", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>