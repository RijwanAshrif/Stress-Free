<?php require_once('../../../private/init.php'); ?>

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = new Response();

if(Helper::is_post()) {
    $action = Helper::post_val("action");
    
    if($action){
        switch($action){

            case MAIN_ACTION:

                $featured_page = Helper::post_val("featured_page");
                $page = Helper::post_val("page");
                $user_id = Helper::post_val("user_id");
                if($page){
                    $admin_token = ADMIN_TOKEN;
                    $user_token = Session::get_session(new User_Token());
                    
                    if(!empty($user_token) && !empty($admin_token)) {
                        $response = API_Helper::main($admin_token, $user_token->user_token, $user_id, $page, $featured_page);

                        if($page == 1){
                            $all_tracks = API_Helper::track_all($admin_token, $user_token->user_token, $user_id, $page);
                            $response->data["tracks"] =  $all_tracks->data["tracks"];
                            $response->data["genres"] =  $all_tracks->data["genres"];
                        }

                    }else $response->create(201, NOT_AUTHORIZE, null);
                }else $response->create(201, INVALID_PARAMETER, null);

                break;


            case INCREASE_LISTENING_COUNT_ACTION :

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $user_id = Helper::post_val('user_id');
                        $track_id = Helper::post_val('track_id');

                        if($track_id){

                            $response = API_Helper::increase_view_count($track_id, $user_id);

                        }else $response->create(201, INVALID_PARAMETER, null);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;

            case TRACK_DETAIL_ACTION:
                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();
                    
                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $user_id = Helper::post_val("user_id");
                        $track_id = Helper::post_val("track_id");
                        
                        if($track_id){

                            $response = API_Helper::getTrackDetail($track_id, $user_id);

                        }else $response->create(201, INVALID_PARAMETER, null);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case SEARCHED_TERMS_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){
                        
                        $response = API_Helper::searched_terms($admin_token_db->admin_id);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case SEARCH_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $track_id = Helper::escape(Helper::post_val("track_id"));
                        $searched = Helper::escape(Helper::post_val("searched"));
                        $user_id = Helper::post_val("user_id");
                        $page = Helper::post_val("page");
                        
                        if($page == 1){
                            $response_arr["tracks"] = API_Helper::get_tracks($admin_token_db->admin_id, $user_id, $page);
                            $response_arr["tags"] = API_Helper::get_tags($admin_token_db->admin_id);
                            $response_arr["genres"] = API_Helper::get_genres($admin_token_db->admin_id);
                        }

                        if($track_id){
                            $response_arr["track_list"] = [API_Helper::getTrackDetail($track_id, $user_id)->data];
                        }else{

                            $response_arr["track_list"] = API_Helper::search_track($page, $searched, $user_id, $admin_token_db->admin_id);
                        }

                        $response->create(200, "Success", $response_arr);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case PROFILE_ACTION:

                $user_id = Helper::post_val("user_id");
                if($user_id){
                    $admin_token = ADMIN_TOKEN;
                    $user_token = Session::get_session(new User_Token());

                    if(!empty($user_token) && !empty($admin_token)) {

                        $admin_token_db = new Setting();
                        $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();


                        $user_token_db = new User_Token();
                        $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                        if(!empty($admin_token_db) && !empty($user_token_db)){

                            $user = new User();
                            $user = $user->where(["id" => $user_id])->one("id, type, username, email, gender, image_name, resolution");

                            $response_user = [];

                            if(!empty($user)) {
                                $image_name = $user->image_name;
                                $user->image_name = null;
                                $response_user = $user->to_valid_array();
                                
                                if(strpos($image_name, 'https://') !== false) $response_user["image_link"] = $image_name;
                                else {
                                    if(!empty($image_name)) $response_user["image_link"] = IMAGE_LINK . $image_name;
                                    else $response_user["image_link"] = IMAGE_LINK . DEFAULT_IMAGE;
                                }
                            }

                            $response_arr["user"] = $response_user;
                            $response_arr["tracks"] = API_Helper::get_tracks($admin_token_db->admin_id, $user_id, 1);
                            $response_arr["tags"] = API_Helper::get_tags($admin_token_db->admin_id);
                            $response_arr["genres"] = API_Helper::get_genres($admin_token_db->admin_id);

                            $response->create(200, "Success", $response_arr);

                        }else $response->create(201, "You are not authorized to view the website", null);
                    }else $response->create(201, "You are not authorized to view the website", null);
                }else $response->create(201, INVALID_PARAMETER, null);

                break;



            case REGISTER_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();


                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $user = new User();
                        $user->type = USER_TYPE_EMAIL;
                        $user->social_id = "";
                        $user->username = Helper::post_val("username");
                        $user->gender = Helper::post_val("gender");
                        $user->email = Helper::post_val("email");
                        $user->admin_id = $admin_token_db->admin_id;
                        $user->created = date(DATE_FORMAT);

                        $generated_password = bin2hex(openssl_random_pseudo_bytes(6));


                        $user->validate_with(["username", "gender", "email", "admin_id", "created"]);
                        $errors = $user->get_errors();

                        if($errors->is_empty()){
                            $existing_user = new User();
                            $existing_user = $existing_user->where(["email" => $user->email])->one();

                            $user->password = password_hash($generated_password, PASSWORD_BCRYPT);

                            if(empty($existing_user)){
                                $user->id = $user->save();
                                if($user->id > 0) $success = true;

                            }else{
                                $user->id = $existing_user->id;
                                $user->created = $existing_user->created;

                                if($user->where(["id" => $existing_user->id])->update())  $success = true;
                            }

                            if($success){
                                $site_config = new Site_Config();
                                $site_config = $site_config->where(["admin_id" => $user->admin_id])->one();

                                $mail_body = main_body($user->email, $generated_password, $site_config->title);
                                $mail_sent = send($mail_body, "User Registration", $site_config->title, $user->email, $user->admin_id);

                                if($mail_sent === true){
                                    $response->create(200, "Success.", $user->to_valid_array());

                                } else $response->create(201, $mail_sent, null);
                            } else $response->create(201, "Something Went Wrong. Please try Again.", null);

                        }else $response->create(201, $errors->get_error_str(), null);


                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;



                case FORGOT_PASSWORD_ACTION:

                    $admin_token = ADMIN_TOKEN;
                    $user_token = Session::get_session(new User_Token());
                    if(!empty($user_token) && !empty($admin_token)) {

                        $admin_token_db = new Setting();
                        $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                        $user_token_db = new User_Token();
                        $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();


                        if(!empty($admin_token_db) && !empty($user_token_db)){

                            $user = new User();
                            $user->type = USER_TYPE_EMAIL;
                            $user->email = Helper::post_val("email");
                            
                            $user->validate_with(["type", "email"]);
                            $errors = $user->get_errors();

                            if($errors->is_empty()){
                                $existing_user = new User();
                                $existing_user = $existing_user->where(["email" => $user->email])
                                    ->andWhere(["type" => USER_TYPE_EMAIL])
                                    ->one();

                                if(!empty($existing_user)){

                                    $updated_user = new User();
                                    $generated_password = bin2hex(openssl_random_pseudo_bytes(6));
                                    $updated_user->password = password_hash($generated_password, PASSWORD_BCRYPT);


                                    if($updated_user->where(["id" => $existing_user->id])->update()){
                                        $site_config = new Site_Config();
                                        $site_config = $site_config->where(["admin_id" => $existing_user->admin_id])->one();



                                        $mail_body = main_body($existing_user->email, $generated_password, $site_config->title);
                                        $mail_sent = send($mail_body, "Forgot Password", $site_config->title, $existing_user->email, $existing_user->admin_id);


                                        if($mail_sent === true){

                                            $response->create(200, "Success.", $user->to_valid_array());

                                        } else $response->create(201, $mail_sent, null);

                                    }else $response->create(201, "Something Went Wrong", null);
                                }else $response->create(201, "Invalid User", null);
                            }else $response->create(201, $errors->get_error_str(), null);
                        }else $response->create(201, INVALID_TOKEN, null);
                    }else $response->create(201, INVALID_TOKEN, null);

                    break;


            case LOGIN_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();


                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $user = new User();
                        $user->type = USER_TYPE_EMAIL;
                        $user->email = Helper::post_val("email");
                        $user->password = Helper::post_val("password");
                        
                        
                        $user->validate_with(["type", "email", "password"]);
                        $errors = $user->get_errors();

                        if($errors->is_empty()){
                            $existing_user = new User();
                            $existing_user = $existing_user->where(["email" => $user->email])
                                ->andWhere(["type" => USER_TYPE_EMAIL])
                                ->one();

                            if(!empty($existing_user)){

                                if(password_verify($user->password, $existing_user->password)){

                                    Helper::setting_logged_in_user($existing_user);

                                    $response->create(200, "Success", $existing_user->to_valid_array());

                                }else $response->create(201, "Invalid Email/Password", null);

                            }else $response->create(201, "Invalid Email", null);
                        }else $response->create(201, $errors->get_error_str(), null);
                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case TAGS_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $page = Helper::post_val("page");
                        $user_id = Helper::post_val("user_id");
                        $tag_id = Helper::post_val("tag_id");

                        if($page == 1){
                            $response_arr["tracks"] = API_Helper::get_tracks($admin_token_db->admin_id, $user_id, $page);
                            $response_arr["tags"] = API_Helper::get_tags($admin_token_db->admin_id);
                            $response_arr["genres"] = API_Helper::get_genres($admin_token_db->admin_id);
                        }


                        if($tag_id){
                            $response_arr["tag_tracks"] = API_Helper::tracks_by_tag($tag_id, $page, $user_id, $admin_token_db->admin_id);
                            if($page == 1){
                                $response_arr["tag_detail"] = API_Helper::tag_detail($tag_id);
                                
                                $track_count = new Track();
                                $track_count = $track_count->where(["status"=>STATUS_ACTIVE])
                                    ->like(["tags" => ',' . $tag_id . ','])->search()->count();

                                $response_arr["tag_detail"]["count"] = $track_count;
                            }

                        }else $response_arr["tags"] = API_Helper::tags($page, $admin_token_db->admin_id, $user_id);


                        $response->create(200, "Success", $response_arr);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;



            case GENRES_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $page = Helper::post_val("page");
                        $user_id = Helper::post_val("user_id");
                        $genre_id = Helper::post_val("genre_id");

                        if($page == 1){
                            $response_arr["tracks"] = API_Helper::get_tracks($admin_token_db->admin_id, $user_id, $page);
                            $response_arr["tags"] = API_Helper::get_tags($admin_token_db->admin_id);
                            $response_arr["genres"] = API_Helper::get_genres($admin_token_db->admin_id);
                        }


                        if($genre_id){

                            $response_arr["genre_tracks"] = API_Helper::tracks_by_genre($genre_id, $page, $user_id, $admin_token_db->admin_id);

                           if($page == 1) {
                               $response_arr["genre_detail"] = API_Helper::genre_detail($genre_id);

                               $track_count = new Track();
                               $track_count = $track_count->where(["status"=>STATUS_ACTIVE])
                                   ->like(["genres" => ',' . $genre_id . ','])->search()->count();

                               $response_arr["genre_detail"]["count"] = $track_count;
                           }

                        }else $response_arr["genres"] = API_Helper::genres($page, $admin_token_db->admin_id, $user_id);

                        $response->create(200, "Success", $response_arr);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case DOWNLOAD_ACTION:
                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());

                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();
                    
                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $track_id = Helper::post_val("id");
                        $user_id = Helper::post_val("user_id");

                        if($user_id && $track_id){

                            $encoded["id"] = $track_id;
                            $encoded["time"] = date(DATE_FORMAT);

                            $response->create(200, "Success", urlencode(Encryption::encrypt(ENCRYPTION_KEY, ENCRYPTION_IV, json_encode($encoded))));

                        }else $response->create(201, INVALID_PARAMETER, null);
                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case PLAYLISTS_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $page = Helper::post_val("page");
                        $user_id = Helper::post_val("user_id");
                        $playslist_id = Helper::post_val("playlist_id");

                        if($page == 1){
                            $response_arr["tracks"] = API_Helper::get_tracks($admin_token_db->admin_id, $user_id, $page);
                            $response_arr["tags"] = API_Helper::get_tags($admin_token_db->admin_id);
                            $response_arr["genres"] = API_Helper::get_genres($admin_token_db->admin_id);
                        }

                        
                        if($playslist_id){

                            $response_arr["playlist_tracks"] = API_Helper::tracks_by_playlist($playslist_id, $page, $user_id, $admin_token_db->admin_id);

                            if($page == 1){
                                $response_arr["playlist_detail"] = API_Helper::playlist_detail($playslist_id);

                                $tracks_of_playlist_count = new Playlist_Track();
                                $tracks_of_playlist_count = $tracks_of_playlist_count->where(["playlist_id" => $playslist_id])->count();

                                $response_arr["playlist_detail"]["count"] = $tracks_of_playlist_count;
                            }

                        }else{
                            $response_arr["playlists"] = API_Helper::playlists($page, $admin_token_db->admin_id, $user_id);
                        }

                        $response->create(200, "Success", $response_arr);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case ARTISTS_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $page = Helper::post_val("page");
                        $user_id = Helper::post_val("user_id");
                        $artist_id = Helper::post_val("artist_id");

                        if($page == 1){
                            $response_arr["tracks"] = API_Helper::get_tracks($admin_token_db->admin_id, $user_id, $page);
                            $response_arr["tags"] = API_Helper::get_tags($admin_token_db->admin_id);
                            $response_arr["genres"] = API_Helper::get_genres($admin_token_db->admin_id);
                        }


                        if($artist_id){

                            $response_arr["artist_tracks"] = API_Helper::tracks_by_artists($artist_id, $page, $user_id, $admin_token_db->admin_id);

                            if($page == 1){
                                $response_arr["artist_detail"] = API_Helper::artist_detail($artist_id);
                                $response_arr["artist_detail"]["count"] = count($response_arr["artist_tracks"]);
                            }

                        }else{
                            $response_arr["artists"] = API_Helper::artists($page, $admin_token_db->admin_id);
                        }

                        $response->create(200, "Success", $response_arr);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;



            case ALBUMS_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $page = Helper::post_val("page");
                        $user_id = Helper::post_val("user_id");
                        $album_id = Helper::post_val("album_id");

                        if($page == 1){
                            $response_arr["tracks"] = API_Helper::get_tracks($admin_token_db->admin_id, $user_id, 1);
                            $response_arr["tags"] = API_Helper::get_tags($admin_token_db->admin_id);
                            $response_arr["genres"] = API_Helper::get_genres($admin_token_db->admin_id);
                        }

                        
                        if($album_id){

                            $response_arr["album_tracks"] = API_Helper::tracks_by_album($album_id, $page, $user_id, $admin_token_db->admin_id);

                            if($page == 1){
                                $response_arr["album_detail"] = API_Helper::album_detail($album_id);
                                $response_arr["album_detail"]["count"] = count($response_arr["album_tracks"]);
                            }

                        }else{
                            $response_arr["albums"] = API_Helper::albums($page, $admin_token_db->admin_id);
                        }

                        $response->create(200, "Success", $response_arr);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;

            case SAVE_PLAYLIST_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $playlist_id = Helper::post_val('playlist_id');
                        $user_id = Helper::post_val('user_id');

                        if($playlist_id && $user_id){

                            $response = API_Helper::save_playlist($playlist_id, $user_id);

                        }else $response->create(201, INVALID_PARAMETER, null);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case MY_MUSIC_ACTION :
                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $user_id = Helper::post_val("user_id");

                        if($user_id){

                            $response_arr["tracks"] = API_Helper::get_tracks($admin_token_db->admin_id, $user_id, 1);
                            $response_arr["tags"] = API_Helper::get_tags($admin_token_db->admin_id);
                            $response_arr["genres"] = API_Helper::get_genres($admin_token_db->admin_id);

                            $saved = new Saved_Playlist();
                            $saved = $saved->where(["user_id" => $user_id])->all();

                            $saved_id_arr = [];
                            $saved_playlist_arr = [];
                            foreach ($saved as $item){
                                $saved_id_arr[$item->playlist_id] = $item->playlist_id;

                                $saved_playlist = new Playlist();
                                $saved_playlist = $saved_playlist->where(["id" => $item->playlist_id])->one("id, title, created, image_name");

                                if(!empty($saved_playlist)) {
                                    $saved_playlist->create_property("saved", 1);
                                    array_push($saved_playlist_arr, Helper::format_image($saved_playlist));
                                }

                            }

                            $response_arr["saved_playlist"] = $saved_playlist_arr;


                            $my_playlist = new Playlist();
                            $my_playlist = $my_playlist->where(["user_id" => $user_id])->all("id, title, created, image_name");


                            $my_playlist_arr = [];
                            foreach ($my_playlist as $item){
                                if(key_exists($item->id, $saved_id_arr)) $item->create_property("saved", 1);
                                else $item->create_property("saved", 2);
                                array_push($my_playlist_arr, Helper::format_image($item));
                            }
                            $response_arr["my_playlist"] = $my_playlist_arr;


                            $favourite = new Favourite();
                            $favourite = $favourite->where(["user_id" =>$user_id])->all();
                            $favourite_arr = [];
                            foreach ($favourite as $item) {
                                $track = new Track();
                                $track = $track->where(["id" => $item->track_id])->one();

                                $formatted_tracks =  Helper::format_track($track);
                                $formatted_tracks["favourited"] = 1;
                                array_push($favourite_arr, $formatted_tracks);
                            }

                            $response_arr["my_favourite"] = $favourite_arr;

                            $response->create(200, "Success", $response_arr);

                        }else $response->create(201, "Please login to access this page", null);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case SONG_DOWNLOAD_ACTION :
                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $id = Helper::post_val("id");
                        $user_id = Helper::post_val("user_id");

                        if($title && $link && $user_id){

                            $track = new Track();
                            $track = $track->where(["id" => $id])->one();

                            if(!empty($track)){

                                $file = $link;

                                if(!file_exists($file)) die("I'm sorry, the file doesn't seem to exist.");

                                $type = filetype($file);
                                // Get a date and timestamp
                                $today = date("F j, Y, g:i a");
                                $time = time();
                                // Send file headers
                                header("Content-type: $type");
                                header("Content-Disposition: attachment;filename={$link}");
                                header("Content-Transfer-Encoding: binary");
                                header('Pragma: no-cache');
                                header('Expires: 0');
                                // Send the file contents.
                                set_time_limit(0);
                                readfile($file);

                            }else $response->create(201, "Invalid Track", null);
                        }else $response->create(201, INVALID_PARAMETER, null);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case SONG_DETAIL_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $id = Helper::post_val("id");
                        $user_id = Helper::post_val("user_id");

                        if($id){
                            $track = new Track();

                            $track = $track->where(["id" => $id])->andWhere(["status"=>STATUS_ACTIVE])->one();

                            if(!empty($track)){
                                $response->create(200, "Success", Helper::format_track_detail($track, $user_id));
                            }else $response->create(201, "Nothing Found", null);

                        }else $response->create(201, "Invalid Track", null);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;



            case UN_SAVE_PLAYLIST_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $playlist_id = Helper::post_val('playlist_id');
                        $user_id = Helper::post_val('user_id');

                        if($playlist_id && $user_id){

                            $response = API_Helper::un_save_playlist($playlist_id, $user_id);

                        }else $response->create(201, INVALID_PARAMETER, null);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case ADD_TO_FAVOURITE_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $track_id = Helper::post_val('track_id');
                        $user_id = Helper::post_val('user_id');

                        if($track_id && $user_id){

                            $response = API_Helper::add_to_favorite($track_id, $user_id);

                        }else $response->create(201, INVALID_PARAMETER, null);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case REMOVE_FAVOURITE_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $track_id = Helper::post_val('track_id');
                        $user_id = Helper::post_val('user_id');

                        if($track_id && $user_id){

                            $response = API_Helper::remove_from_favourite($track_id, $user_id);

                        }else $response->create(201, INVALID_PARAMETER, null);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case REMOVE_FROM_PLAYLIST_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $track_id = Helper::post_val('track_id');
                        $playlist_id = Helper::post_val('playlist_id');
                        $user_id = Helper::post_val('user_id');

                        if($track_id && $playlist_id && $user_id){

                            $response = API_Helper::removeFromPlayList($playlist_id, $track_id, $user_id);

                        }else $response->create(201, INVALID_PARAMETER, null);


                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;

            case ADD_TO_PLAYLIST_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $track_id = Helper::post_val('track_id');
                        $playlist_id = Helper::post_val('playlist_id');
                        $user_id = Helper::post_val('user_id');

                        if($track_id && $playlist_id && $user_id){

                            $response = API_Helper::addToPlayList($playlist_id, $track_id, $user_id);
                            
                        }else $response->create(201, INVALID_PARAMETER, null);


                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;
            
            
            case DELETE_PLAYLIST_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){
                        
                        $playlist_id = Helper::post_val('playlist_id');
                        $user_id = Helper::post_val('user_id');

                        if($playlist_id && $user_id){

                            $playlist_from_db = new Playlist();
                            $playlist_from_db = $playlist_from_db->where(["id" => $playlist_id])->one();

                            if(!empty($playlist_from_db)){

                                if($user_id == $playlist_from_db->user_id){
                                    $playlist = new Playlist();
                                    if($playlist->where(["id" => $playlist_id])->delete()){

                                        $playlist->id = $playlist_id;

                                        $playlist_track = new Playlist_Track();
                                        $playlist_track->where(["playlist_id" => $playlist_id])->delete();
                                        $response->create(200, "Success", $playlist->to_valid_array());

                                    }else $response->create(201, "Something went wrong. Please try again", null);

                                }else $response->create(201, "You can only delete your own playlist", null);
                            }else $response->create(201, "Unknown Playlist", null);
                        }else $response->create(201, INVALID_PARAMETER, null);


                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;



                case CREATE_PLAYLIST_ACTION:

                    $admin_token = ADMIN_TOKEN;
                    $user_token = Session::get_session(new User_Token());
                    if(!empty($user_token) && !empty($admin_token)) {

                        $admin_token_db = new Setting();
                        $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                        $user_token_db = new User_Token();
                        $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                        if(!empty($admin_token_db) && !empty($user_token_db)){

                                $id = Helper::post_val('id');
                                $title = Helper::post_val('title');
                                $user_id = Helper::post_val('user_id');

                                if($title){
                                    $playlist = new Playlist();
                                    $playlist->title = $title;
                                    $success = false;

                                    if($id){

                                        $playlist_from_db = new Playlist();
                                        $playlist_from_db = $playlist_from_db->where(["id" => $id])->one();

                                        if(!empty($playlist_from_db)) if($playlist->where(["id" => $id])->update()) {
                                            $success = true;
                                            $playlist->id = $playlist_from_db->id;
                                            $playlist->user_id = $playlist_from_db->user_id;

                                        } else $response->create(201, "Invalid Playlist", null);

                                    }else {

                                        if($user_id){
                                            $playlist->user_id = $user_id;
                                            $playlist->admin_id = $admin_token_db->admin_id;
                                            $playlist->created = date(DATE_FORMAT);
                                            $playlist->id = $playlist->save();

                                            if($playlist->id > 0) $success = true;
                                        }else $response->create(201, INVALID_PARAMETER, null);
                                    }

                                    if($success) $response->create(200, "Success", $playlist);
                                    else $response->create(201, "Something went wrong. Please try again", null);


                            }else $response->create(201, INVALID_PARAMETER, null);


                        }else $response->create(201, INVALID_TOKEN, null);
                    }else $response->create(201, INVALID_TOKEN, null);

                    break;

            case PLAYLIST_BY_USER_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $user_id = Helper::post_val('user_id');
                        $page = Helper::post_val('page');

                        if($user_id){

                            $response->create(200, "Success", API_Helper::playlist_by_user($page, $user_id));

                        }else $response->create(201, INVALID_PARAMETER, null);


                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case PROFILE_INFO_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();

                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $user = new User();
                        $user->id = Helper::post_val("id");
                        $user->username = Helper::post_val("username");
                        $user->gender = Helper::post_val("gender");

                        $response = API_Helper::update_profile($user);

                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;


            case UPDATE_PASSWORD_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();


                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $id = Helper::post_val("id");
                        $old_password = Helper::post_val("old_password");
                        $new_password = Helper::post_val("new_password");
                        $confirm_password = Helper::post_val("confirm_password");

                        if($id && $old_password && $new_password && $confirm_password){

                            if($new_password === $confirm_password){

                                $user_from_db = new User();
                                $user_from_db = $user_from_db->where(["id" => $id])->one();

                                if(!empty($user_from_db)){
                                    if(password_verify($old_password, $user_from_db->password)){

                                        $update_user = new User();
                                        $update_user->password = password_hash($new_password, PASSWORD_BCRYPT);
                                        
                                        if($update_user->where(["id" => $id])->update()){

                                            $response_obj["user"]["text"]["old_password"] = "";
                                            $response_obj["user"]["text"]["new_password"] = "";
                                            $response_obj["user"]["text"]["confirm_password"] = "";
                                            $response_obj["redirect"] = $user_from_db->id;

                                            Session::unset_session(new User());

                                            $response->create(200, "Password updated Successfully.", $response_obj);

                                        }else $response->create(201, "Something went wrong", null);


                                    }else $response->create(201, WRONG_PASSWORD, null);
                                }else $response->create(201, INVALID_USER, null);

                            }else $response->create(201, PASSWORD_NOT_MATCH, null);
                        }else $response->create(201, INVALID_PARAMETER, null);
                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;

            case USER_IMAGE_ACTION:

                $admin_token = ADMIN_TOKEN;
                $user_token = Session::get_session(new User_Token());
                if(!empty($user_token) && !empty($admin_token)) {

                    $admin_token_db = new Setting();
                    $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

                    $user_token_db = new User_Token();
                    $user_token_db = $user_token_db->where(["user_token" => $user_token->user_token])->one();


                    if(!empty($admin_token_db) && !empty($user_token_db)){

                        $uploaded_image = Helper::file_val("image_name");
                        $id = Helper::post_val("id");

                        if($id && $uploaded_image){

                            $response = API_Helper::profile_image($id, $uploaded_image);

                        }else $response->create(201, INVALID_PARAMETER, null);
                    }else $response->create(201, INVALID_TOKEN, null);
                }else $response->create(201, INVALID_TOKEN, null);

                break;

            default:
                $response->create(201, "Something Went Missing", null);
                break;
        }

    }else $response->create(201, "Invalid Action", null);

}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();


function main_body($receiver_email, $password, $company_name){

    $mail_body  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    $mail_body .= '<html xmlns="http://www.w3.org/1999/xhtml" lang="en" style="width: 100%; height: 100%;">';

    $mail_body .= '<head>';
    $mail_body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    $mail_body .= '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>';
    $mail_body .= '</head>';
    $mail_body .= '<body style="height: 100%; width: 100%; margin: 0; padding: 0;">';


    $mail_body .= '<div style="width: 100%; height: 100%; padding: 0px; " >';
    $mail_body .= '<div style="width: 700px;">';

    $mail_body .= '<h5 style="font-size: 16px;">To finish the registration process, We need to make sure this email is yours.</h5>';
    $mail_body .= '<h3 style="font-weight: 400; font-size: 2em; line-height: 1; ">Please use this credentials below to login</h3>';

    $mail_body .= '<br>';
    $mail_body .= '<table>';
    $mail_body .= '<tbody>';
    $mail_body .= '<tr>';

    $mail_body .= '<td>';


    $mail_body .= '<p><b>Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </b>' . $receiver_email . '</p>';
    $mail_body .= '<p><b>Password &nbsp;&nbsp;: </b>' . $password . '</p>';

    $mail_body .= '</td>';

    $mail_body .= '</tr>';
    $mail_body .= '</tbody>';
    $mail_body .= '</table>';


    $mail_body .= '<br>';

    $mail_body .= '<p style="font-size: 16px;">If you did not register in ' . $company_name . ', You can safely ignore this email.</p>';
    $mail_body .= '<p style="font-size: 16px;">Someone else might have typed your email by mistake.</p>';
    $mail_body .= '<br>';
    $mail_body .= '<br>';
    $mail_body .= '<p style="font-size: 16px;">Thanks</p>';
    $mail_body .= '<p style="font-size: 16px;">The ' . ucfirst($company_name) . ' Team</p>';

    $mail_body .= '</div>';
    $mail_body .= '</div>';
    $mail_body .= '</body>';
    $mail_body .= '</html>';

    return $mail_body;
}


function send($mail_body, $mail_subject, $company_name, $receiver_email, $admin_id){
    $mail = new PHPMailer(true);
    try {
        $mail->ClearAllRecipients( );

        $mail->isSMTP();

        $smtp_config = new Smtp_Config();
        $smtp_config = $smtp_config->where(["admin_id" => $admin_id])->one();

        $mail->Host = trim($smtp_config->host);
        $mail->SMTPAuth = true;
        $mail->Username = trim($smtp_config->username);
        $mail->Password = trim($smtp_config->smtp_password);
        $mail->SMTPSecure = trim($smtp_config->encryption);
        $mail->Port = trim($smtp_config->port);


        $mail->setFrom(trim($smtp_config->sender_email), $company_name);
        $mail->addAddress($receiver_email);


        $mail->isHTML(true);

        $mail->Subject = $mail_subject;

        $mail->Body = $mail_body;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->send();
        return true;

    } catch (Exception $e) {
        return 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
}


?>