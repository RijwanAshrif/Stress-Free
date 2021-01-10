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
            $track = $track->where(["id" => $id])->one("id, lyrics");

            $text_field["id"] = $track->id;
            $text_field["lyrics"] = $track->lyrics;

            $response_obj["track"]["text"]["id"] = $track->id;
            $response_obj["track"]["wshywyg"]["lyrics"] = $track->lyrics;
        }

        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){
        
        $track = new Track();

        $track->id = Helper::post_val("id");
        $track->lyrics = Helper::post_val("lyrics");
        
        if($track->id && $track->lyrics){

            if($track->where(["id" => $track->id])->update()){

                $response_obj["track"]["text"]["id"] = $track->id;
                $response_obj["track"]["wshywyg"]["lyrics"] = $track->lyrics;
                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Something Went Wrong", null);
        }else $response->create(201, "Required Field is missing", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>
