<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");
        $response_obj["artist"]["image"]["image_name"] = [];
        $response_obj["artist"]["text"] = [];
        $response_obj["artist"]["switch"]["status"] = STATUS_ACTIVE;

        if($id){
            $artist = new Artist();
            $artist = $artist->where(["id" => $id])->one("name, genres, image_name, description, status");

            $text_field["name"] = $artist->name;
            $text_field["description"] = $artist->description;

            if(!empty($artist->image_name)) $image_field["image_name"] = $artist->image_name;
            else $image_field["image_name"] = DEFAULT_IMAGE;

            $search_dropdown = [];
            
            if(!empty($artist->genres)){
                $single_genre_arr = explode(",", $artist->genres);

                $genre_arr = [];
                foreach ($single_genre_arr as $item){
                    $item = trim($item);
                    $current_genre = new Genre();
                    $current_genre = $current_genre->where(["id" => $item])->one("title");
                    if(!empty($current_genre)) $genre_arr[$item] = $current_genre->title;
                }
                $search_dropdown["genres"] = $genre_arr;
            }

            $response_obj["artist"]["search_dropdown"] = $search_dropdown;

            $response_obj["artist"]["text"] = $text_field;
            $response_obj["artist"]["switch"]["status"] = $artist->status;
            $response_obj["artist"]["image"] = $image_field;
        }

        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){
        $artist = new Artist();
        $artist->id = Helper::post_val("id");
        
        $artist->name = Helper::post_val("name");
        $artist->description = Helper::post_val("description");

        $artist->status = (isset($_POST['status'])) ? 1 : 2;
        $artist->admin_id = $admin->id;
        $artist->genres = Helper::post_val("genres");

        $artist->validate_with(["name", "genres", "description", "status", "admin_id"]);
        $errors = $artist->get_errors();
        $success = false;

        if($errors->is_empty()){
            if($artist->id){
                if($artist->where(["id" => $artist->id])->update()) $success = true;
            }else{
                $artist->created = date(DATE_FORMAT);
                $artist->id = $artist->save();
                if($artist->id > 0) {
                    $response_obj["redirect"] = "id=" . $artist->id;
                    $success = true;
                }
            }

            if($success){
                $response_obj["artist"]["switch"]["status"] = $artist->status;
                $artist->admin_id = null;
                $artist->status = null;
                $response_obj["artist"]["text"] = $artist->to_valid_array();
                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Something Went Wrong", null);
        }else $response->create(201, "Required Field is missing", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>