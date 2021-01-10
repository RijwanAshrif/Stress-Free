<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");

        $response_obj["track"]["text"] = [];

        if($id){
            $track = new Track();
            $track = $track->where(["id" => $id])->one("description");

            $text_field["description"] = $track->description;

            $response_obj["track"]["text"] = $text_field;
        }

        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){
        $track = new Track();
        $track->id = Helper::post_val("id");

        $track->description = Helper::post_val("description");

        $track->admin_id = $admin->id;

        $track->validate_with(["description"]);
        $errors = $track->get_errors();
        $success = false;

        if($errors->is_empty()){
            if($track->id){
                if($track->where(["id" => $track->id])->update()) $success = true;
            }else{

                $track->created = date(DATE_FORMAT);
                $track->id = $track->save();
                if($track->id > 0) {
                    $response_obj["redirect"] = "id=" . $track->id;
                    $success = true;
                }
            }

            if($success){

                $track->admin_id = null;
                $track->status = null;
                $response_obj["track"]["text"] = $track->to_valid_array();
                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Something Went Wrong", null);
        }else $response->create(201, "Required Field is missing", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>
