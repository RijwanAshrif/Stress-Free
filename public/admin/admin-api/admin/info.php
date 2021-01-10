<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $admin_profile = new Admin();
        $admin_profile = $admin->where(["id" => $admin->id])->one();
        $admin_profile->resolution = null;

        $text_field["id"] = $admin_profile->id;
        $text_field["first_name"] = $admin_profile->first_name;
        $text_field["last_name"] = $admin_profile->last_name;
        $text_field["location"] = $admin_profile->location;
        $text_field["phone"] = $admin_profile->phone;
        $text_field["speaks"] = $admin_profile->speaks;

        $wshywyg_field["description"] = $admin_profile->description;

        if(!empty($admin_profile->image_name)) $image_field["image_name"] = $admin_profile->image_name;
        else $image_field["image_name"] = DEFAULT_IMAGE;

        $response_obj["admin"]["text"] = $text_field;
        $response_obj["admin"]["image"] = $image_field;
        $response_obj["admin"]["wshywyg"] = $wshywyg_field;

        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){

        $admin_profile = new Admin();
        $id = Helper::post_val("id");
        $admin_profile->first_name = Helper::post_val("first_name");
        $admin_profile->last_name = Helper::post_val("last_name");
        $admin_profile->location = Helper::post_val("location");
        $admin_profile->phone = Helper::post_val("phone");
        $admin_profile->speaks = Helper::post_val("speaks");
        $admin_profile->description = Helper::post_val("description");

        if($admin_profile->where(["id" => $id])->update()){


            $response_obj["admin"]["text"] = $admin_profile->to_valid_array();
            $response->create(200, "Success", $response_obj);

        }else $response->create(201, "Something went wrong.", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>