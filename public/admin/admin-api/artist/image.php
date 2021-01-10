<?php require_once('../../../../private/init.php'); ?>
<?php

$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)){
    $errors = new Errors();
    $message = new Message();

    if (Helper::is_post()) {
        $artist = new Artist();
        $artist->admin_id = $admin->id;

        $uploaded_image = Helper::file_val("image_name");
        $id = Helper::post_val("id");

        if($uploaded_image){
            $upload = new Upload($uploaded_image);
            $upload->set_max_size(MAX_IMAGE_SIZE);
            $upload->set_folder(ADMIN_UPLOAD_FOLDER);
            if($upload->upload()) {
                $artist->image_name = $upload->get_file_name();
                $artist->resolution = $upload->resolution;
            }

            $errors = $upload->get_errors();

            
            if($errors->is_empty()){

                if($id){
                    $image_from_db = new Artist();
                    $image_from_db = $image_from_db->where(["id" => $id])->one("image_name");

                    if(!empty($image_from_db)){
                        if($artist->where(["id" => $id])->update()) {

                            Upload::delete(ADMIN_UPLOAD_FOLDER, $image_from_db->image_name);
                            $response_obj["artist"]["image"]["image_name"] = $artist->image_name;
                            $response->create(200, "Success.", $response_obj);

                        }else $response->create(201, "Something went wrong please try again.", null);
                    }else $response->create(201, "Invalid Configuration ID.", null);
                }else{

                    $artist->status = STATUS_DEACTIVE;
                    $artist->id = $artist->save();

                    if($artist->id > 0) {

                        $response_obj["artist"]["image"]["image_name"] = $artist->image_name;
                        $response_obj["artist"]["redirect"] = "id=" . $artist->id;
                        $response->create(200, "Success.", $response_obj);

                    }else $response->create(201, "Something went wrong please try again.", null);
                }

            }else $response->create(201, $upload->get_errors()->to_sting(), null);

        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "No Session Found", null);

echo $response->print_response();

?>