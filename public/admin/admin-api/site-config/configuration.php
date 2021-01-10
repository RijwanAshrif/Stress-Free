<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $site_config = new Site_Config();
        $site_config = $site_config->where(["admin_id" => $admin->id])->one();
        $site_config->resolution = null;

        $text_field["id"] = $site_config->id;
        $text_field["title"] = $site_config->title;
        $text_field["tag_line"] = $site_config->tag_line;

        $response_obj["site_config"]["text"] = $text_field;

        if(!empty($site_config->image_name)) $image_field["image_name"] = $site_config->image_name;
        else $image_field["image_name"] = DEFAULT_IMAGE;

        $response_obj["site_config"]["image"] = $image_field;

        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){

        $site_config = new Site_Config();
        $id = Helper::post_val("id");
        $site_config->title = Helper::post_val("title");
        $site_config->tag_line = Helper::post_val("tag_line");

        if($site_config->where(["id" => $id])->update()){
            $response_obj["site_config"]["text"] = $site_config->to_valid_array();
            $response->create(200, "Success", $response_obj);

        }else $response->create(201, "Something went wrong.", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>