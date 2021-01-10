<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");

        if($id){
            $track = new Track();
            $track = $track->where(["id" => $id])->one("track_name, album, audio_link, title, status, audio_type, image_name, resolution, tags, genres, artists");

            if(!empty($track)){

                $text_field["audio_link"] = $track->audio_link;
                $text_field["title"] = $track->title;
                $response_obj["track"]["text"] = $text_field;
                $response_obj["track"]["image"]["image_name"] = $track->image_name;
                $response_obj["track"]["audio"]["track_name"] =  $track->track_name;

                $image_dropdown = [];
                if(!empty($track->album)){
                    $current_album = new Album();
                    $current_album = $current_album->where(["id" => $track->album])->one("id, title, image_name");

                    if(!empty($current_album)){
                        $image_dropdown["id"] = $current_album->id;
                        $image_dropdown["title"] = $current_album->title;
                        $image_dropdown["image_name"] = $current_album->image_name;
                    }
                }
                
                $response_obj["track"]["image_dropdown"]["album"] =  $image_dropdown;

                $search_dropdown = [];

                if(!empty($track->tags)){
                    $all_tags = new Tag();
                    $all_tags = $all_tags->where(["admin_id" => $admin->id])->all("id, title");
                    $all_tag_arr = [];
                    foreach ($all_tags as $item) {
                        $all_tag_arr[$item->id] = $item->title;
                    }

                    $single_tag_arr = explode(",", $track->tags);

                    $tags_arr = [];
                    foreach ($single_tag_arr as $item){
                        $item = trim($item);
                        if(key_exists($item, $all_tag_arr)) $tags_arr[$item] = $all_tag_arr[$item];
                    }

                    $search_dropdown["tags"] = $tags_arr;
                }

                if(!empty($track->genres)){
                    $single_genre_arr = explode(",", $track->genres);

                    $genre_arr = [];
                    foreach ($single_genre_arr as $item){
                        $item = trim($item);
                        $current_genre = new Genre();
                        $current_genre = $current_genre->where(["id" => $item])->one("title");
                        if(!empty($current_genre)) $genre_arr[$item] = $current_genre->title;
                    }
                    $search_dropdown["genres"] = $genre_arr;
                }

                if(!empty($track->artists)){
                    $single_artist_arr = explode(",", $track->artists);

                    $artist_arr = [];
                    foreach ($single_artist_arr as $item){
                        $item = trim($item);
                        $current_artist = new Artist();
                        $current_artist = $current_artist->where(["id" => $item])->one("name");
                        if(!empty($current_artist)) $artist_arr[$item] = $current_artist->name;
                    }
                    $search_dropdown["artists"] = $artist_arr;
                }

                $response_obj["track"]["search_dropdown"] = $search_dropdown;
                
                $response_obj["track"]["switch"]["status"] = $track->status;
                $response_obj["track"]["radio"]["audio_type"] = $track->audio_type;

                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Invalid Track", null);

        }else {

            $response_obj["track"]["image"]["image_name"] = [];
            $response_obj["track"]["audio"] = [];
            $response_obj["track"]["search_dropdown"] = [];
            $response_obj["track"]["text"] = [];
            $response_obj["track"]["switch"]["status"] = STATUS_ACTIVE;
            $response_obj["track"]["radio"]["audio_type"] = 1;
            $response_obj["track"]["image_dropdown"]["album"] =  [];

            $response->create(200, "Success", $response_obj);
        }
        
    }else if(Helper::is_post()){
        $track = new Track();
        $track->id = Helper::post_val("id");

        $track->title = Helper::post_val("title");
        $track->tags = Helper::post_val("tags");
        $track->artists = Helper::post_val("artists");
        $track->genres = Helper::post_val("genres");
        $track->album = Helper::post_val("album");

        if(!$track->tags) $track->tags  = -999;

        $track->status = (isset($_POST['status'])) ? 1 : 2;


        $track->audio_type = Helper::post_val('audio_type');
        $track->admin_id = $admin->id;

        $track->validate_with(["title", "status", "audio_type", "admin_id", "artists", "genres"]);
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

                $response_obj["track"]["radio"]["audio_type"] = $track->audio_type;
                $response_obj["track"]["switch"]["status"] = $track->status;
                $track->admin_id = null;
                $track->status = null;
                $track->audio_type = null;
                $response_obj["track"]["text"] = $track->to_valid_array();
                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Something Went Wrong", null);
        }else $response->create(201, "Required Field is missing", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>
