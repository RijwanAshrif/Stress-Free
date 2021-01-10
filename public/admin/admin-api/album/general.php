<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");
        $response_obj["album"]["image"]["image_name"] = [];
        $response_obj["album"]["search_dropdown"] = [];
        $response_obj["album"]["text"] = [];
        $response_obj["album"]["switch"]["status"] = STATUS_ACTIVE;
        $response_obj["album"]["switch"]["featured"] = STATUS_DEACTIVE;

        if($id){
            $album = new Album();
            $album = $album->where(["id" => $id])->one("title, image_name, status, featured, tags, genres, artists");

            $text_field["title"] = $album->title;

            $search_dropdown = [];

            if(!empty($album->tags)){
                $all_tags = new Tag();
                $all_tags = $all_tags->where(["admin_id" => $admin->id])->all("id, title");
                $all_tag_arr = [];
                foreach ($all_tags as $item) {
                    $all_tag_arr[$item->id] = $item->title;
                }

                $single_tag_arr = explode(",", $album->tags);

                $tags_arr = [];
                foreach ($single_tag_arr as $item){
                    $item = trim($item);
                    if(key_exists($item, $all_tag_arr)) $tags_arr[$item] = $all_tag_arr[$item];
                }

                $search_dropdown["tags"] = $tags_arr;
            }

            if(!empty($album->genres)){
                $single_genre_arr = explode(",", $album->genres);

                $genre_arr = [];
                foreach ($single_genre_arr as $item){
                    $item = trim($item);
                    $current_genre = new Genre();
                    $current_genre = $current_genre->where(["id" => $item])->one("title");
                    if(!empty($current_genre)) $genre_arr[$item] = $current_genre->title;
                }
                $search_dropdown["genres"] = $genre_arr;
            }

            if(!empty($album->artists)){
                $single_artist_arr = explode(",", $album->artists);

                $artist_arr = [];
                foreach ($single_artist_arr as $item){
                    $item = trim($item);
                    $current_artist = new Artist();
                    $current_artist = $current_artist->where(["id" => $item])->one("name");
                    if(!empty($current_artist)) $artist_arr[$item] = $current_artist->name;
                }
                $search_dropdown["artists"] = $artist_arr;
            }


            if(!empty($album->image_name)) $image_field["image_name"] = $album->image_name;
            else $image_field["image_name"] = DEFAULT_IMAGE;

            $response_obj["album"]["search_dropdown"] = $search_dropdown;
            $response_obj["album"]["text"] = $text_field;
            $response_obj["album"]["switch"]["status"] = $album->status;
            $response_obj["album"]["switch"]["featured"] = (!empty($album->featured)) ? $album->featured : 2;
            $response_obj["album"]["image"] = $image_field;
        }

        $response->create(200, "Success", $response_obj);

    }else if(Helper::is_post()){
        $album = new Album();
        $album->id = Helper::post_val("id");

        $album->title = Helper::post_val("title");
        $album->tags = Helper::post_val("tags");
        $album->artists = Helper::post_val("artists");
        $album->genres = Helper::post_val("genres");

        if(!$album->tags) $album->tags  = -999;

        $album->status = (isset($_POST['status'])) ? 1 : 2;
        $album->featured = (isset($_POST['featured'])) ? 1 : 2;
        $album->admin_id = $admin->id;

        $album->validate_with(["title", "status", "featured", "admin_id", "artists", "genres"]);
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

                $response_obj["album"]["switch"]["featured"] = $album->featured;
                $response_obj["album"]["switch"]["status"] = $album->status;
                $album->admin_id = null;

                $response_obj["album"]["text"] = $album->to_valid_array();
                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Something Went Wrong", null);
        }else $response->create(201, "Required Field is missing", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>
