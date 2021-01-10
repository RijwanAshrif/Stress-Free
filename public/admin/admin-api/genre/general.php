<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");
        $response_obj["genre"]["text"] = [];

        if($id){
            $genre = new Genre();
            $genre = $genre->where(["id" => $id])->one("title");

            $text_field["title"] = $genre->title;

            $response_obj["genre"]["text"] = $text_field;
        }

        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){
        $genre = new Genre();
        $genre->id = Helper::post_val("id");
        
        $genre->title = Helper::post_val("title");

        $genre->admin_id = $admin->id;

        $genre->validate_with(["title", "admin_id"]);
        $errors = $genre->get_errors();
        $success = false;

        if($errors->is_empty()){
            if($genre->id){
                if($genre->where(["id" => $genre->id])->update()) $success = true;
            }else{
                $genre->created = date(DATE_FORMAT);
                $genre->id = $genre->save();
                if($genre->id > 0) {
                    $response_obj["redirect"] = "id=" . $genre->id;
                    $success = true;
                }
            }

            if($success){
                $genre->admin_id = null;
                $response_obj["genre"]["text"] = $genre->to_valid_array();
                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Something Went Wrong", null);
        }else $response->create(201, "Required Field is missing", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>