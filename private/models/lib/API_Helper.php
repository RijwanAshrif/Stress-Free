<?php

class API_Helper{

    public static function profile_image($id, $file){
        $response = new Response();

        $existing_user = new User();
        $existing_user = $existing_user->where(["id" => $id])->one();

        if(!empty($existing_user)){
            $user = new User();
            $upload = new Upload($file);
            $upload->set_max_size(MAX_IMAGE_SIZE);

            if($upload->upload()) {
                $user->image_name = $upload->get_file_name();
                $user->resolution = $upload->resolution;
            }
            $errors = $upload->get_errors();
            if($errors->is_empty()){

                if($user->where(["id" => $existing_user->id])->update()){

                    Upload::delete(UPLOAD_FOLDER , $existing_user->image_name);

                    $response_arr["resolution"] = $user->resolution;
                    $response_arr["image_link"] = IMAGE_LINK . $user->image_name;
                    $response_arr["thumb_link"] = THUMB_LINK . $user->image_name;

                    $response->create(200, "Success", $response_arr);

                }else $response->create(201, "Something went wrong. Please try again", null);

            } else $response->create(201, $errors->get_error_str(), null);
        } else $response->create(201, "Invalid User", null);

        return $response;
    }



    public static function main($admin_token, $user_token, $user_id, $page, $featured_page){
        $response = new Response();


        if($admin_token && $user_token){
            $admin_token_db = new Setting();
            $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

            $user_token_db = new User_Token();
            $user_token_db = $user_token_db->where(["user_token" => $user_token])->one();

            if(!empty($admin_token_db) && !empty($user_token_db)){

                if($page == 1){

                    $featured_album = new Album();
                    $featured_album = $featured_album->where(["admin_id" => $admin_token_db->admin_id])->andWhere(["status" => STATUS_ACTIVE])
                        ->andWhere(["featured" => STATUS_ACTIVE])
                        ->orderBy("created")->orderType("DESC")->all("id, title, image_name, resolution, artists");

                    $featured_album_assoc = [];
                    $featured_album_arr = [];
                    foreach ($featured_album as $item){

                        if(empty($featured_album_assoc[$item->id])) {
                            $artist_ids = explode(",", $item->artists);

                            $artist_name_with_id = [];

                            $artist_names = "";
                            foreach ($artist_ids as $artist_id){
                                $artist = new Artist();
                                $artist = $artist->where(["id" => $artist_id])->one("id, name");
                                if(!empty($artist)) {
                                    $artist_names .= $artist->name . ", ";

                                    array_push($artist_name_with_id, $artist->to_valid_array());
                                }
                            }

                            $item->create_property("artist_array", $artist_name_with_id);
                            $item->artists = substr($artist_names, 0, -2);
                            array_push($featured_album_arr, Helper::format_image($item));
                        }
                    }

                    $response_obj["featured_album"] = $featured_album_arr;

                    $recent_album = new Album();
                    $recent_album = $recent_album->where(["admin_id" => $admin_token_db->admin_id])
                        ->andWhere(["status" => STATUS_ACTIVE])
                        ->not(["featured" => STATUS_ACTIVE])
                        ->limit(0, API_PAGINATION)->orderBy("created")->orderType("DESC")
                        ->all("id, title, image_name, resolution, artists");

                    $recent_album_arr = [];


                    foreach ($recent_album as $item){
                        $artist_ids = explode(",", $item->artists);
                        $artist_name_with_id = [];

                        $artist_names = "";
                        foreach ($artist_ids as $artist_id){
                            $artist = new Artist();
                            $artist = $artist->where(["id" => $artist_id])->one("id, name");
                            if(!empty($artist)) {
                                $artist_names .= $artist->name . ", ";
                                array_push($artist_name_with_id, $artist->to_valid_array());
                            }
                        }

                        $item->create_property("artist_array", $artist_name_with_id);
                        $item->artists = substr($artist_names, 0, -2);
                        array_push($recent_album_arr, Helper::format_image($item));
                    }

                    $response_obj["recent_album"] = $recent_album_arr;

                    $popular_artist = new Artist();
                    $popular_artist = $popular_artist->where(["admin_id" => $admin_token_db->admin_id])->andWhere(["status" => STATUS_ACTIVE])
                        ->limit(0, API_PAGINATION)->orderBy("listening_count")->orderType("DESC")
                        ->all("id, name, image_name, resolution");

                    $popular_artist_arr = [];
                    foreach ($popular_artist as $item){
                        array_push($popular_artist_arr, Helper::format_image($item));
                    }

                    $response_obj["popular_artist"] = $popular_artist_arr;
                    $tags = new Tag();
                    $tags = $tags->where(["admin_id" => $admin_token_db->admin_id])->all("id, title");
                    $response_obj["tags"] = $tags;

                    $genres = new Genre();
                    $genres = $genres->where(["admin_id" => $admin_token_db->admin_id])->all("id, title");
                    $response_obj["genres"] = $genres;


                }


                $saved_playlist_arr = [];
                if($user_id) {
                    $saved_playlist = new Saved_Playlist();
                    $saved_playlist = $saved_playlist->where(["user_id" => $user_id])->all();

                    foreach ($saved_playlist as $item) {
                        $saved_playlist_arr[$item->playlist_id] = $item->playlist_id;
                    }
                }

                $featured_playlist = new Playlist();

                if($page) {
                    $start = ($page - 1) * API_PAGINATION;
                    $featured_playlist = $featured_playlist->where(["admin_id" => $admin_token_db->admin_id])
                        ->andWhere(["featured" => STATUS_ACTIVE])
                        ->andWhere(["status" => STATUS_ACTIVE])
                        ->limit($start, API_PAGINATION)
                        ->orderBy("saving_count")->orderType("DESC")->all("id, title, user_id, image_name, resolution");



                }else{
                    $featured_playlist = $featured_playlist->where(["admin_id" => $admin_token_db->admin_id])
                        ->andWhere(["featured" => STATUS_ACTIVE])
                        ->andWhere(["status" => STATUS_ACTIVE])
                        ->orderBy("saving_count")->orderType("DESC")->all("id, title, user_id, image_name, resolution");
                }


                $playlist_arr = [];

                if(count($featured_playlist) > 0){

                    $featured_page = $page;


                    foreach ($featured_playlist as $item){
                        if($user_id){
                            if(key_exists($item->id, $saved_playlist_arr)) $item->create_property("saved", 1);
                            else $item->create_property("saved", 2);
                        } else $item->create_property("saved", 2);

                        array_push($playlist_arr, Helper::format_image($item));
                    }

                    $response_obj["featured_playlist"] = $playlist_arr;


                }else{


                    $popular_playlist = new Playlist();

                    if($page) {

                        $popular_page = $page - $featured_page;

                        $start = ($popular_page - 1) * API_PAGINATION;

                        if($start < 1) $start = 0;

                        $popular_playlist = $popular_playlist->where(["admin_id" => $admin_token_db->admin_id])
                            ->andWhere(["status" => STATUS_ACTIVE])
                            ->andWhere(["featured" => STATUS_DEACTIVE])
                            ->limit($start, API_PAGINATION)
                            ->orderBy("saving_count")->orderType("DESC")->all();






                    }else{

                        $popular_playlist = $popular_playlist->where(["admin_id" => $admin_token_db->admin_id])
                            ->andWhere(["status" => STATUS_ACTIVE])
                            ->andWhere(["featured" => STATUS_DEACTIVE])
                            ->orderBy("saving_count")->orderType("DESC")->all("id, title, user_id, image_name, resolution");
                    }


                    foreach ($popular_playlist as $item){
                        if($user_id){
                            if(key_exists($item->id, $saved_playlist_arr)) $item->create_property("saved", 1);
                            else $item->create_property("saved", 2);
                        } else $item->create_property("saved", 2);

                        array_push($playlist_arr, Helper::format_image($item));
                    }

                    $response_obj["popular_playlist"] = $playlist_arr;

                }

                $response_obj["featured_page"] = $featured_page;

                $response->create(200, "Success", $response_obj);

            }else $response->create(201, INVALID_TOKEN, null);
        }else $response->create(201, INVALID_TOKEN, null);

        return $response;
    }


    public function format_playlist(){


    }

    public static function increase_view_count($track_id, $user_id){
        $response = new Response();

        $track_from_db = new Track();
        $track_from_db = $track_from_db->where(["id" => $track_id])->one("id, view_count, artists");

        if($user_id) {

            $user_from_db = new User();
            $user_from_db = $user_from_db->where(["id" => $user_id])->one("id");

            if (!empty($user_from_db)) {

                $recently_played = new Recently_Played();
                $recently_played->user_id = $user_id;
                $recently_played->track_id = $track_id;
                $recently_played->created = date(DATE_FORMAT);

                $recently_played_from_db = new Recently_Played();
                $recently_played_from_db = $recently_played_from_db->where(["user_id" => $user_id])
                    ->where(["track_id" => $track_id])->one();

                $recently_playing_count = new Recently_Played();
                $recently_playing_count = $recently_playing_count->where(["user_id" => $user_id])->count();

                if (empty($recently_played_from_db)) {
                    if (($recently_playing_count >= TRACK_HISTORY)) {

                        $lastItem = $recently_played_from_db[count($recently_played_from_db) - 1];
                        if ($recently_played->where(["id" => $lastItem->id])->update()) {
                            $recently_played->id = $lastItem->id;
                        }

                    } else {
                        $recently_played->id = $recently_played->save();
                    }
                }
            }else $response->create(201, "Invalid User", null);
        }


        if(!empty($track_from_db)){

            $updated_track = new Track();
            $updated_track->view_count = ($track_from_db->view_count + 1);

            $track_from_db->view_count = $updated_track->view_count;

            if($updated_track->where(["id" => $track_from_db->id])->update()) {

                $artist_ids = explode(",", $track_from_db->artists);
                foreach ($artist_ids as $item){
                    $artist = new Artist();
                    $artist = $artist->where(["id" => $item])->one();
                    if(!empty($artist)) {
                        $updated_artist = new Artist();
                        $updated_artist->listening_count = $artist->listening_count + 1;
                        $updated_artist->where(["id" => $artist->id])->update();
                    }
                }

                $track_from_db->artists = null;
                $response->create(200, "Success", $track_from_db->to_valid_array());

            } else $response->create(201, "Something went wrong. Please try again", null);

        }else $response->create(201, "Invalid Track", null);


        return $response;
    }


    public static function searched_terms($admin_id){
        $response = new Response();

        $popular_search_term = new Search_Term();
        $popular_search_term = $popular_search_term->where(["admin_id" => $admin_id])
            ->limit(0, SEARCH_TERM_COUNT)->orderBy("count")->orderType("DESC")->all("id, term, count, created");

        $response_obj["popular"] = $popular_search_term;

        $recent_search_term = new Search_Term();
        $recent_search_term = $recent_search_term->where(["admin_id" => $admin_id])
            ->limit(0, SEARCH_TERM_COUNT)->orderBy("created")->orderType("DESC")->all("id, term, count, created");

        $response_obj["recent"] = $recent_search_term;

        $response->create(200, "Success", $response_obj);

        return $response;
    }

    public static function search_track($page, $searched, $user_id, $admin_id){
        $tracks = new Track();

        if($searched){
            $searched_arr = explode(' ', $searched);

            $searched_tags = '';
            foreach ($searched_arr as $item){
                $tags = new Tag();
                $tags = $tags->where(["admin_id" => $admin_id])->like(["title" => $item])->search()->all();

                foreach ($tags as $inner_item){
                    if(!empty($inner_item)) $searched_tags .= ',' . $inner_item->id . ',' . ' ';
                }
            }

            if($page) {
                $start = ($page - 1) * API_PAGINATION;
                $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                    ->like(["tags" => $searched_tags])->like(["title" => $searched])->search()
                    ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();

                if($page == 1){
                    self::insert_searched_terms($admin_id, $searched);
                }

            }else{
                $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                    ->like(["tags" => $searched_tags])->like(["title" => $searched])->search()
                    ->orderBy("created")->orderType("DESC")->all();
            }

        }else {

            if($page) {
                $start = ($page - 1) * API_PAGINATION;
                $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                    ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
            }else{
                $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                    ->orderBy("created")->orderType("DESC")->all();
            }
        }

        $response_obj = [];
        foreach ($tracks as $item){

            array_push($response_obj, Helper::format_track($item, $user_id));
        }

        return $response_obj;
    }


    function insert_searched_terms($admin_id, $searched){

        $search_term_frm_db = new Search_Term();
        $search_term_frm_db = $search_term_frm_db->where(["term" => $searched])->one();

        $search_term = new Search_Term();
        $search_term->created = date(DATE_FORMAT);
        $success = false;

        if(!empty($search_term_frm_db)){
            $search_term->count = ($search_term_frm_db->count + 1);

            if($search_term->where(["id" => $search_term_frm_db->id])->update()) {
                $search_term->term = $search_term_frm_db->term;
                $search_term->id = $search_term_frm_db->id;
                $success = true;
            }
        }else{
            $search_term->term = $searched;
            $search_term->admin_id = $admin_id;
            $search_term->count = 1;
            $search_term->id = $search_term->save();
            if($search_term->id > 0) $success = true;
        }

    }

    public static function track_all($admin_token, $user_token, $user_id, $page){
        $response = new Response();

        if($admin_token && $user_token){

            $admin_token_db = new Setting();
            $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

            $user_token_db = new User_Token();
            $user_token_db = $user_token_db->where(["user_token" => $user_token])->one();

            if(!empty($admin_token_db) && !empty($user_token_db)){

                $response_obj["tracks"] = self::get_tracks($admin_token_db->admin_id, $user_id, $page);

                $response_obj["genres"] = [];

                if($page == 1){
                    $genres = new Genre();
                    $genres = $genres->where(["admin_id" => $admin_token_db->admin_id])
                        ->orderBy("created")->orderType("DESC")->all("id, title");

                    $response_obj["genres"] = $genres;
                }

                $response->create(200, "Success", $response_obj);

            }else $response->create(201, INVALID_TOKEN, null);
        }else $response->create(201, INVALID_TOKEN, null);

        return $response;
    }


    public static function tag_detail($tag_id){
        $tag = new Tag();
        $tag = $tag->where(["id" => $tag_id])->one("title, id");
        return $tag->to_valid_array();
    }


    public static function tracks_by_tag($tag_id, $page, $user_id, $admin_id){
        $tracks = new Track();

        $tag_id = ',' . $tag_id . ',';

        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->like(["tags" => $tag_id])->search()
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
        }else{
            $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->like(["tags" => $tag_id])->search()
                ->orderBy("created")->orderType("DESC")->all();
        }

        $response_obj = [];
        foreach ($tracks as $item){

            array_push($response_obj, Helper::format_track($item, $user_id));
        }

        return $response_obj;
    }


    public static function genres($page, $admin_id){
        $genre = new Genre();

        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $genre = $genre->where(["admin_id" => $admin_id])
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all("id, title");
        }else{
            $genre = $genre->where(["admin_id" => $admin_id])
                ->orderBy("created")->orderType("DESC")->all("id, title");
        }

        return $genre;
    }


    public static function genre_detail($genre_id){
        $genre = new Genre();
        $genre = $genre->where(["id" => $genre_id])->one("title, id");
        return $genre->to_valid_array();
    }


    public static function tracks_by_genre($genre_id, $page, $user_id, $admin_id){
        $tracks = new Track();

        $genre_id = ',' . $genre_id . ',';

        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->like(["genres" => $genre_id])->search()
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
        }else{
            $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->like(["genres" => $genre_id])->search()
                ->orderBy("created")->orderType("DESC")->all();
        }

        $response_obj = [];
        foreach ($tracks as $item){

            array_push($response_obj, Helper::format_track($item, $user_id));
        }

        return $response_obj;
    }


    public static function tags($page, $admin_id){
        $tags = new Tag();

        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $tags = $tags->where(["admin_id" => $admin_id])
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all("id, title");
        }else{
            $tags = $tags->where(["admin_id" => $admin_id])
                ->orderBy("created")->orderType("DESC")->all("id, title");
        }

        $tag_arr = [];
        foreach ($tags as $item){
            array_push($tag_arr, Helper::format_tag($item));
        }

        return $tag_arr;
    }


    public static function playlist_detail($playlist_id){
        $playlist = new Playlist();
        $playlist = $playlist->where(["id" => $playlist_id])->one("title, image_name, resolution");
        return Helper::format_image($playlist);
    }


    public static function tracks_by_playlist($playlist_id, $page, $user_id){
        $tracks_of_playlist = new Playlist_Track();
        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $tracks_of_playlist = $tracks_of_playlist->where(["playlist_id" => $playlist_id])
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
        }else{
            $tracks_of_playlist = $tracks_of_playlist->where(["playlist_id" => $playlist_id])
                ->orderBy("created")->orderType("DESC")->all();
        }

        $response_obj = [];

        if(!empty($tracks_of_playlist)) {
            $playlist = new Playlist();
            $playlist = $playlist->where(["id" => $playlist_id])->one();


            foreach ($tracks_of_playlist as $item) {

                $track = new Track();
                $track = $track->where(["id" => $item->track_id])->andWhere(["status" => STATUS_ACTIVE])->one();

                if (!empty($track)) {

                    $formatted_track = Helper::format_track($track, $user_id);
                    if($user_id == $playlist->user_id) $formatted_track["added_to_playlist"] = 1;
                    else $formatted_track["added_to_playlist"] = 2;
                    array_push($response_obj, $formatted_track);
                }
            }
        }

        return $response_obj;
    }


    public static function playlists($page, $admin_id, $user_id){
        $all_playlist = new Playlist();

        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $all_playlist = $all_playlist->where(["admin_id" => $admin_id])
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all("id, title, user_id, image_name, resolution");
        }else{
            $all_playlist = $all_playlist->where(["admin_id" => $admin_id])
                ->orderBy("created")->orderType("DESC")->all("id, title, user_id, image_name, resolution");
        }

        $saved_playlist_arr = [];
        if($user_id) {
            $saved_playlist = new Saved_Playlist();
            $saved_playlist = $saved_playlist->where(["user_id" => $user_id])->all();

            foreach ($saved_playlist as $item) {
                $saved_playlist_arr[$item->playlist_id] = $item->playlist_id;
            }
        }

        $playlist_arr = [];
        foreach ($all_playlist as $item){

            if($user_id){
                if(key_exists($item->id, $saved_playlist_arr)) $item->create_property("saved", 1);
                else $item->create_property("saved", 2);
            } else $item->create_property("saved", 2);

            array_push($playlist_arr, Helper::format_image($item));

        }
        return $playlist_arr;
    }




    public static function getTrackDetail($id, $user_id){
        $response = new Response();

        if($id){
            $track = new Track();
            $track = $track->where(["id" => $id])->andWhere(["status"=>STATUS_ACTIVE])->one();

            if(!empty($track)){
                $response->create(200, "Success", Helper::format_track_detail($track, $user_id));
            }else $response->create(201, "Nothing Found", null);

        }else $response->create(201, "Invalid Track", null);

        return $response;
    }


    public static function removeFromPlayListAdmin($playlist_id, $track_id){
        $response = new Response();

        $playlist_from_db = new Playlist();
        $playlist_from_db = $playlist_from_db->where(["id" => $playlist_id])->one();

        if(!empty($playlist_from_db)){

            $playlist_track_from_db = new Playlist_Track();
            $playlist_track_from_db = $playlist_track_from_db->where(["track_id" => $track_id])
                ->andWhere(["playlist_id" => $playlist_id])->one();

            if(!empty($playlist_track_from_db)){

                $playlist_track = new Playlist_Track();
                if($playlist_track->where(["id" => $playlist_track_from_db->id])->delete()){

                    $response->create(200, "Success.", $playlist_track_from_db->to_valid_array());

                }else $response->create(201, "Something went wrong. Please try again.", null);
            }else $response->create(201, "You did't add this track in your playlist", null);


        }else $response->create(201, "Invalid Playlist", null);

        return $response;
    }

    public static function addToPlayListAdmin($playlist_id, $track_id){
        $response = new Response();

        $playlist = new Playlist();
        $playlist = $playlist->where(["id" => $playlist_id])->one();

        if(!empty($playlist)){

            $track_from_db = new Track();
            $track_from_db = $track_from_db->where(["id" => $track_id])->one();

            if(!empty($track_from_db)){

                $playlist_track_frm_db = new Playlist_Track();
                $playlist_track_frm_db = $playlist_track_frm_db->where(["playlist_id" => $playlist_id])
                    ->andWhere(["track_id" => $track_id])->one();

                if(empty($playlist_track_frm_db)){

                    $playlist_track = new Playlist_Track();
                    $playlist_track->playlist_id = $playlist_id;
                    $playlist_track->track_id = $track_id;
                    $playlist_track->created = date(DATE_FORMAT);

                    $playlist_track->id = $playlist_track->save();

                    if($playlist_track->id > 0) {
                        $response->create(200, "Success", $playlist_track);

                        if(!empty($track_from_db->image_name) && $playlist->image_uploaded_by_admin != STATUS_ACTIVE){

                            $playlist_image = new Playlist();
                            $playlist_image->image_name = $track_from_db->image_name;
                            $playlist_image->resolution = $track_from_db->resolution;

                            $playlist_image->where(["id" => $playlist_id])->update();
                        }

                    } else $response->create(201, "Something went wrong. Please try again.", null);

                }else $response->create(200, "Track already added", null);
            }else $response->create(201, "Unknown Track", null);

        }else $response->create(201, "Unknown Playlist", null);

        return $response;
    }



    public static function removeFromPlayList($playlist_id, $track_id, $user_id){
        $response = new Response();

        $playlist_from_db = new Playlist();
        $playlist_from_db = $playlist_from_db->where(["id" => $playlist_id])->one();

        if(!empty($playlist_from_db)){
            if($playlist_from_db->user_id == $user_id){

                $playlist_track_from_db = new Playlist_Track();
                $playlist_track_from_db = $playlist_track_from_db->where(["track_id" => $track_id])
                    ->andWhere(["playlist_id" => $playlist_id])->one();

                if(!empty($playlist_track_from_db)){

                    $playlist_track = new Playlist_Track();
                    if($playlist_track->where(["id" => $playlist_track_from_db->id])->delete()){

                        $response->create(200, "Track Removed.", $playlist_track_from_db->to_valid_array());

                    }else $response->create(201, "Something went wrong. Please try again.", null);
                }else $response->create(201, "You did't add this track in your playlist", null);

            }else $response->create(201, "This is not your Playlist", null);
        }else $response->create(201, "Invalid Playlist", null);

        return $response;
    }



    public static function addToPlayList($playlist_id, $track_id, $user_id){
        $response = new Response();

        $playlist = new Playlist();
        $playlist = $playlist->where(["id" => $playlist_id])->one();

        if(!empty($playlist)){

            if($playlist->user_id == $user_id){

                $track_from_db = new Track();
                $track_from_db = $track_from_db->where(["id" => $track_id])->one();

                if(!empty($track_from_db)){

                    $playlist_track_frm_db = new Playlist_Track();
                    $playlist_track_frm_db = $playlist_track_frm_db->where(["playlist_id" => $playlist_id])
                        ->andWhere(["track_id" => $track_id])->one();

                    if(empty($playlist_track_frm_db)){

                        $playlist_track = new Playlist_Track();
                        $playlist_track->playlist_id = $playlist_id;
                        $playlist_track->track_id = $track_id;
                        $playlist_track->created = date(DATE_FORMAT);

                        $playlist_track->id = $playlist_track->save();

                        if($playlist_track->id > 0) {
                            $response->create(200, "Track Added", $playlist_track);

                            if(!empty($track_from_db->image_name) && $playlist->image_uploaded_by_admin != STATUS_ACTIVE){

                                $playlist_image = new Playlist();
                                $playlist_image->image_name = $track_from_db->image_name;
                                $playlist_image->resolution = $track_from_db->resolution;

                                $playlist_image->where(["id" => $playlist_id])->update();
                            }

                        } else $response->create(201, "Something went wrong. Please try again.", null);

                    }else $response->create(200, "Track already added", null);
                }else $response->create(201, "Unknown Track", null);

            }else $response->create(201, "You can add song only your own Playlist", null);
        }else $response->create(201, "Unknown Playlist", null);

        return $response;
    }


    public static function save_playlist($playlist_id, $user_id){
        $response = new Response();

        $playlist_from_db = new Playlist();
        $playlist_from_db = $playlist_from_db->where(["id" => $playlist_id])->one();

        $user_from_db = new User();
        $user_from_db = $user_from_db->where(["id" => $user_id])->one();

        if(!empty($playlist_from_db) && !empty($user_from_db)){

            $saved_playlist_from_db = new Saved_Playlist();
            $saved_playlist_from_db = $saved_playlist_from_db->where(["playlist_id" => $playlist_id])
                ->andWhere(["user_id" =>$user_id])->one();

            if(empty($saved_playlist_from_db)){

                $saved_playlist = new Saved_Playlist();
                $saved_playlist->user_id = $user_id;
                $saved_playlist->playlist_id = $playlist_id;
                $saved_playlist->created = date(DATE_FORMAT);

                $saved_playlist->id = $saved_playlist->save();

                if($saved_playlist->id > 0) {

                    $updated_playlist = new Playlist();
                    $updated_playlist->saving_count = $playlist_from_db->saving_count + 1;
                    $updated_playlist->where(["id" => $playlist_from_db->id])->update();

                    $response->create(200, "Success", $saved_playlist->to_valid_array());
                } else $response->create(201, "Something went wrong, Please try again", null);

            }else $response->create(201, "Playlist already saved", null);

        }else $response->create(201, "Invalid Playlist/User", null);
        return $response;
    }

    public static function un_save_playlist($playlist_id, $user_id){
        $response = new Response();

        $saved_playlist_from_db = new Saved_Playlist();
        $saved_playlist_from_db = $saved_playlist_from_db->where(["playlist_id" => $playlist_id])
            ->andWhere(["user_id" =>$user_id])->one();

        if(!empty($saved_playlist_from_db)){

            $delete_saved_playlist = new Saved_Playlist();

            if($delete_saved_playlist->where(["id" => $saved_playlist_from_db->id])->delete()){

                $playlist_from_db = new Playlist();
                $playlist_from_db = $playlist_from_db->where(["id" => $playlist_id])->one();

                $updated_playlist = new Playlist();
                $updated_playlist->saving_count = $playlist_from_db->saving_count - 1;
                $updated_playlist->where(["id" => $playlist_from_db->id])->update();


                $response->create(200, "Success", $saved_playlist_from_db->to_valid_array());

            } else $response->create(201, "Something went wrong, Please try again", null);

        }else $response->create(201, "You didn't save the playlist", null);

        return $response;
    }


    public static function remove_from_favourite($track_id, $user_id){
        $response = new Response();
        $track = new Track();
        $track = $track->where(["id" => $track_id])->one();

        $user = new User();
        $user = $user->where(["id" => $user_id])->one();

        if(!empty($track) && !empty($user)){

            $favourite_from_db = new Favourite();
            $favourite_from_db = $favourite_from_db->where(["track_id" => $track_id])
                ->andWhere(["user_id" => $user_id])->one();

            if(!empty($favourite_from_db)){

                $favourite = new Favourite();

                if($favourite->where(["id" => $favourite_from_db->id])->delete()) $response->create(200, 'Track removed successfully.', $favourite_from_db);
                else $response->create(201, "Something went wrong. Please try again", null);

            }else $response->create(201, "Track is not Favourited", null);

        }else $response->create(201, "Invalid Track/User", null);

        return $response;
    }


    public static function add_to_favorite($track_id, $user_id){
        $response = new Response();

        $track = new Track();
        $track = $track->where(["id" => $track_id])->one();

        $user = new User();
        $user = $user->where(["id" => $user_id])->one();

        if(!empty($track) && !empty($user)){

            $favourite_from_db = new Favourite();
            $favourite_from_db = $favourite_from_db->where(["track_id" => $track_id])
                ->andWhere(["user_id" => $user_id])->one();

            if(empty($favourite_from_db)){

                $favourite = new Favourite();
                $favourite->track_id = $track_id;
                $favourite->user_id = $user_id;
                $favourite->created = date(DATE_FORMAT);

                $favourite->id = $favourite->save();

                if($favourite->id > 0) $response->create(200, "Track added", $favourite);
                else $response->create(201, "Something went wrong. Please try again", null);

            }else $response->create(201, "Track Already Favourited", null);

        }else $response->create(201, "Invalid Track/User", null);

        return $response;
    }


    public static function playlist_by_user($page, $user_id){
        $playlist = new Playlist();

        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $playlist = $playlist->where(["user_id" => $user_id])
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
        }else{
            $playlist = $playlist->where(["user_id" => $user_id])
                ->orderBy("created")->orderType("DESC")->all();
        }

        $playlist_arr = [];
        foreach ($playlist as $item){
            $item->title = Helper::decode_entity($item->title);
            array_push($playlist_arr, Helper::format_image($item));
        }

        return $playlist_arr;
    }

    public static function album_detail($album_id){
        $album = new Album();
        $album = $album->where(["id" => $album_id])->one("title, image_name, resolution");
        return Helper::format_image($album);
    }


    public static function tracks_by_album($album_id, $page, $user_id, $admin_id){
        $tracks = new Track();
        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->andWhere(["album" => $album_id])
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
        }else{
            $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->andWhere(["album" => $album_id])
                ->orderBy("created")->orderType("DESC")->all();
        }

        $response_obj = [];
        foreach ($tracks as $item){

            array_push($response_obj, Helper::format_track($item, $user_id));
        }

        return $response_obj;
    }


    public static function albums($page, $admin_id){
        $albums = new Album();

        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $albums = $albums->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
        }else{
            $albums = $albums->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->orderBy("created")->orderType("DESC")->all();
        }

        $response_obj = [];
        foreach ($albums as $item){
            $item->description = null;
            $item->tags = null;
            $item->genres = null;
            $item->artists = null;
            $item->status = null;

            $item->status = null;

            $item->title = Helper::decode_entity($item->title);

            array_push($response_obj, Helper::format_image($item));
        }
        return $response_obj;
    }



    public static function artist_detail($artist_id){
        $artist = new Artist();
        $artist = $artist->where(["id" => $artist_id])->one("name, image_name, resolution");
        return Helper::format_image($artist);
    }


    public static function tracks_by_artists($artist_id, $page, $user_id, $admin_id){
        $tracks = new Track();
        $artist_id = ',' . $artist_id . ',';

        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->like(["artists" => $artist_id])->search()
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
        }else{
            $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->like(["artists" => $artist_id])->search()
                ->orderBy("created")->orderType("DESC")->all();
        }

        $response_obj = [];
        foreach ($tracks as $item){
            array_push($response_obj, Helper::format_track($item, $user_id));
        }

        return $response_obj;
    }



    public static function artists($page, $admin_id){
        $artists = new Artist();

        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $artists = $artists->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
        }else{
            $artists = $artists->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->orderBy("created")->orderType("DESC")->all();
        }

        $response_obj = [];
        foreach ($artists as $item){
            $item->name = Helper::decode_entity($item->name);
            $item->description = null;
            $item->status = null;

            array_push($response_obj, Helper::format_image($item));
        }

        return $response_obj;
    }

    public static function update_profile($user){
        $response = new Response();

        $user->validate_with(["id", "username", "gender", "number"]);
        $errors = $user->get_errors();

        if($errors->is_empty()){

            $existing_user = new User();
            $existing_user = $existing_user->where(["id" => $user->id])->one();

            if(!empty($existing_user)){

                if($user->where(["id" => $user->id ])->update()){

                    $response_user = $user->to_valid_array();
                    $response_user["redirect"] = "";
                    Session::set_session($user);
                    $response->create(200, "Success", $response_user);

                }else $response->create(201, "Something went wrong. Please try again", null);
            }else $response->create(201, "Invalid User.", null);
        }else $response->create(201, $errors->get_error_str(), null);

        return $response;
    }

    public static function get_tracks($admin_id, $user_id, $page){
        $tracks = new Track();

        if($page) {
            $start = ($page - 1) * API_PAGINATION;
            $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
        }else{
            $tracks = $tracks->where(["admin_id" => $admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                ->orderBy("created")->orderType("DESC")->all();
        }

        $track_list = [];
        foreach ($tracks as $item){
            array_push($track_list, Helper::format_track($item, $user_id));
        }
        return $track_list;
    }


    public static function get_genres($admin_id){
        $genres = new Genre();
        $genres = $genres->where(["admin_id" => $admin_id])->orderBy("created")->orderType("DESC")->all();
        return $genres;
    }

    public static function get_tags($admin_id){
        $tags = new Tag();
        $tags = $tags->where(["admin_id" => $admin_id])->orderBy("created")->orderType("DESC")->all();
        return $tags;
    }


}