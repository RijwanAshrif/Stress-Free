<?php

require_once('../../../../private/init.php'); ?>

<?php

$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
     if(Helper::is_post()){

         $files = Helper::file_val("tracks");
         $album_id = Helper::post_val("album_id");
         $artist_id = Helper::post_val("artist_id");

         if($album_id || $artist_id){

             if($files){
                 $files_arr = [];

                 for($i = 0; $i < count($files["name"]); $i++){
                     $single_file["name"] = $files["name"][$i];
                     $single_file["type"] = $files["type"][$i];
                     $single_file["tmp_name"] = $files["tmp_name"][$i];
                     $single_file["error"] = $files["error"][$i];
                     $single_file["size"] = $files["size"][$i];

                     array_push($files_arr, $single_file);
                 }

                 if($album_id){
                     $album = new Album();
                     $album = $album->where(["id" => $album_id])->one("artists, genres, tags");
                 }else if($artist_id){
                     $artist = new Artist();
                     $artist = $artist->where(["id" => $artist_id])->one("genres");
                 }


                 $uploaded_files = [];
                 foreach ($files_arr as $file){

                     $new_track = new Track();
                     $new_track->admin_id = $admin->id;



                     $upload = new Upload($file);
                     $upload->set_folder(ADMIN_UPLOAD_FOLDER);
                     $upload->set_max_size(MAX_AUDIO_SIZE);
                     $upload->set_type(AUDIO_FILE);

                     if($upload->upload()) {
                         $new_track->track_name = $upload->get_file_name();

                         $new_track->title = str_replace('.' . $upload->get_file_ext(), "" , $file["name"]);

                         $new_track->track_duration = $upload->get_duration();

                         $new_track->created = date(DATE_FORMAT);

                         if($album_id){
                             $new_track->album = $album_id;
                             $new_track->artists = $album->artists;
                             $new_track->genres = $album->genres;
                             $new_track->tags = $album->tags;
                         }else if($artist_id){
                             $new_track->artists = "," . $artist_id . ",";
                             $new_track->genres = $artist->genres;
                         }

                         $new_track->status = STATUS_ACTIVE;
                         $new_track->audio_type = TRACK_TYPE_AUDIO;
                         $new_track->image_name = $upload->get_audio_image();
                         $new_track->resolution = $upload->get_audio_image_resolution();
                     }
                     $errors = $upload->get_errors();

                     if($errors->is_empty()) {
                         $new_track->id = $new_track->save(["image_name", "resolution"]);
                         if($new_track->id > 0){
                             
                             $response_track["delete"] = $new_track->id;
                             $response_track["edit"] = $new_track->id;
                             $response_track["value"] = Helper::format_tabbed_track($new_track);

                             array_push($uploaded_files, $response_track);
                         }
                     }
                 }

                 if($album_id) $response_obj["album"]["multiple_tracks"] = $uploaded_files;
                 else if($artist_id) $response_obj["artist"]["multiple_tracks"] = $uploaded_files;

                 if(!empty($uploaded_files)) $response->create(200, "Success", $response_obj);
                 else $response->create(201, "Something Went Wrong", null);

             }else $response->create(201, "Invalid Parameter", null);
         }else $response->create(201, "Invalid parameter.", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>