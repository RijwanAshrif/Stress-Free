<?php require_once('../../../../private/init.php'); ?>
<?php



$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)){
    $errors = new Errors();
    $message = new Message();

    if (Helper::is_post()) {
        $playlist = new Playlist();
        $playlist->admin_id = $admin->id;

        $playlist->image_uploaded_by_admin = STATUS_ACTIVE;

        $uploaded_image = Helper::file_val("image_name");
        $id = Helper::post_val("id");

        if($uploaded_image){
            $upload = new Upload($uploaded_image);
            $upload->set_max_size(MAX_IMAGE_SIZE);
            $upload->set_folder(ADMIN_UPLOAD_FOLDER);
            if($upload->upload()) {
                $playlist->image_name = $upload->get_file_name();
                $playlist->resolution = $upload->resolution;
            }

            $errors = $upload->get_errors();
            
            if($errors->is_empty()){

                if($id){
                    $image_from_db = new Playlist();
                    $image_from_db = $image_from_db->where(["id" => $id])->one("image_name");

                    if(!empty($image_from_db)){
                        if($playlist->where(["id" => $id])->update()) {

                            Upload::delete(ADMIN_UPLOAD_FOLDER, $image_from_db->image_name);
                            $response_obj["playlist"]["image"]["image_name"] = $playlist->image_name;
                            $response->create(200, "Success.", $response_obj);


                        }else $response->create(201, "Something went wrong please try again.", null);
                    }else $response->create(201, "Invalid Configuration ID.", null);
                }else{

                    $playlist->status = STATUS_DEACTIVE;
                    $playlist->id = $playlist->save();

                    if($playlist->id > 0) {

                        $response_obj["playlist"]["image"]["image_name"] = $playlist->image_name;
                        $response_obj["playlist"]["redirect"] = "id=" . $playlist->id;
                        $response->create(200, "Success.", $response_obj);

                    }else $response->create(201, "Something went wrong please try again.", null);
                }

            }else $response->create(201, $upload->get_errors()->to_sting(), null);

        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "No Session Found", null);

echo $response->print_response();

?>