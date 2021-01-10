<?php require_once('../../../../private/init.php'); ?>

<?php

$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)){
    $errors = new Errors();
    $message = new Message();

    if (Helper::is_post()) {
        $site_config = new Site_Config();
        $uploaded_image = Helper::file_val("image_name");
        $id = Helper::post_val("id");

        if($uploaded_image && $id){
                $upload = new Upload($uploaded_image);
                $upload->set_max_size(MAX_IMAGE_SIZE);
                $upload->set_folder(ADMIN_UPLOAD_FOLDER);
                if($upload->upload()) {
                    $site_config->image_name = $upload->get_file_name();
                    $site_config->resolution = $upload->resolution;
                }
                $errors = $upload->get_errors();
                if($errors->is_empty()){

                    $image_from_db = new Site_Config();
                    $image_from_db = $image_from_db->where(["id" => $id])->one("image_name");

                    if(!empty($image_from_db)){
                        if($site_config->where(["id" => $id])->update()) {

                            Upload::delete(ADMIN_UPLOAD_FOLDER, $image_from_db->image_name);
                            $response_obj["site_config"]["image"]["image_name"] = $site_config->image_name;
                            $response->create(200, "Success.", $response_obj);

                        }else $response->create(201, "Something went wrong please try again.", null);
                    }else $response->create(201, "Invalid Configuration ID.", null);


                }else $response->create(201, $upload->get_errors()->to_sting(), null);

        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "No Session Found", null);

echo $response->print_response();

?>