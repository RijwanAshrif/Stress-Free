<?php require_once('../../../../private/init.php'); ?>
<?php

$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)){
    $errors = new Errors();
    $message = new Message();

    if (Helper::is_post()) {
        $album = new Album();
        $album->admin_id = $admin->id;

        $uploaded_image = Helper::file_val("image_name");
        $id = Helper::post_val("id");

        if($uploaded_image){
            $upload = new Upload($uploaded_image);
            $upload->set_folder(ADMIN_UPLOAD_FOLDER);
            $upload->set_max_size(MAX_IMAGE_SIZE);
            if($upload->upload()) {

                $album->image_name = $upload->get_file_name();
                $album->resolution = $upload->resolution;
            }
            $errors = $upload->get_errors();
            if($errors->is_empty()){

                if($id){
                    $image_from_db = new Album();
                    $image_from_db = $image_from_db->where(["id" => $id])->one("image_name");

                    if(!empty($image_from_db)){
                        if($album->where(["id" => $id])->update()) {

                            Upload::delete(ADMIN_UPLOAD_FOLDER, $image_from_db->image_name);
                            $response_obj["album"]["image"]["image_name"] = $album->image_name;
                            $response->create(200, "Success.", $response_obj);

                        }else $response->create(201, "Something went wrong please try again.", null);
                    }else $response->create(201, "Invalid Configuration ID.", null);
                }else{
                    $album->status = 2;
                    $album->id = $album->save();

                    if($album->id > 0) {

                        $response_obj["album"]["image"]["image_name"] = $album->image_name;
                        $response_obj["album"]["redirect"] = "id=" . $album->id;
                        $response->create(200, "Success.", $response_obj);

                    }else $response->create(201, "Something went wrong please try again.", null);
                }

            }else $response->create(201, $upload->get_errors()->to_sting(), null);

        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "No Session Found", null);

echo $response->print_response();

?>
