<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $setting = new Setting();
        $setting = $setting->where(["admin_id" => $admin->id])->one("id, admin_token");


        $response_obj["admin_token"]["text"] = $setting->to_valid_array();
        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){

        $setting = new Setting();
        $id = Helper::post_val("id");
        $setting->admin_token = Helper::post_val("admin_token");

        if($setting->where(["id" => $id])->update()){

            $response_obj["admin_token"]["text"] = $setting->to_valid_array();
            $response->create(200, "Success", $response_obj);

        }else $response->create(201, "Something went wrong.", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();



?>