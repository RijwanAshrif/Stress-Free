<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");
        $response_obj["playlist"]["image"]["image_name"] = [];
        $response_obj["playlist"]["text"] = [];
        $response_obj["playlist"]["switch"]["status"] = STATUS_ACTIVE;
        $response_obj["playlist"]["switch"]["featured"] = STATUS_DEACTIVE;

        if($id){
            $playlist = new Playlist();
            $playlist = $playlist->where(["id" => $id])->one();

            $text_field["title"] = $playlist->title;
            $text_field["user_id"] = $playlist->user_id;

            if(!empty($playlist->image_name)) $image_field["image_name"] = $playlist->image_name;
            else $image_field["image_name"] = DEFAULT_IMAGE;

            $response_obj["playlist"]["text"] = $text_field;
            $response_obj["playlist"]["switch"]["status"] = $playlist->status;
            $response_obj["playlist"]["switch"]["featured"] = $playlist->featured;
            $response_obj["playlist"]["image"] = $image_field;
        }

        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){
        $playlist = new Playlist();
        $playlist->id = Helper::post_val("id");

        $playlist->title = Helper::post_val("title");

        $playlist->featured = (isset($_POST['featured'])) ? STATUS_ACTIVE : STATUS_DEACTIVE;
        $playlist->status = (isset($_POST['status'])) ? STATUS_ACTIVE : STATUS_DEACTIVE;
        $playlist->admin_id = $admin->id;

        $playlist->validate_with(["title", "status", "featured", "admin_id"]);
        $errors = $playlist->get_errors();
        $success = false;
        $playlist->user_id = ADMIN_AS_USER_ID;

        if($errors->is_empty()){
            if($playlist->id){
                if($playlist->where(["id" => $playlist->id])->update()) $success = true;
            }else{

                $playlist->created = date(DATE_FORMAT);
                $playlist->id = $playlist->save();
                
                if($playlist->id > 0) {
                    $response_obj["redirect"] = "id=" . $playlist->id;
                    $success = true;
                }
            }

            if($success){
                $response_obj["playlist"]["switch"]["status"] = $playlist->status;
                $response_obj["playlist"]["switch"]["featured"] = $playlist->featured;
                $playlist->admin_id = null;
                $playlist->status = null;
                $response_obj["playlist"]["text"] = $playlist->to_valid_array();
                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Something Went Wrong", null);
        }else $response->create(201, "Required Field is missing", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>