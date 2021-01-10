<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");

        $response_obj["album"]["text"] = [];

        if($id){
            $album = new Album();
            $album = $album->where(["id" => $id])->one("description");

            $text_field["description"] = $album->description;

            $response_obj["album"]["text"] = $text_field;
        }

        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){
        $album = new Album();
        $album->id = Helper::post_val("id");

        $album->description = Helper::post_val("description");

        $album->admin_id = $admin->id;

        $album->validate_with(["description"]);
        $errors = $album->get_errors();
        $success = false;

        if($errors->is_empty()){
            if($album->id){
                if($album->where(["id" => $album->id])->update()) $success = true;
            }else{

                $album->created = date(DATE_FORMAT);
                $album->id = $album->save();
                if($album->id > 0) {
                    $response_obj["redirect"] = "id=" . $album->id;
                    $success = true;
                }
            }

            if($success){

                $album->admin_id = null;
                $album->status = null;
                $response_obj["album"]["text"] = $album->to_valid_array();
                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Something Went Wrong", null);
        }else $response->create(201, "Required Field is missing", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>
