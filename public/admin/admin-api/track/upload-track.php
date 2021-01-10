<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());
$track_image = null;

if(!empty($admin)) {
     if(Helper::is_post()){

         $file = Helper::file_val("track");
         $id = Helper::post_val("id");
         $title = Helper::post_val("title");

         if($id && $file){
             $new_track = new Track();
             $new_track->admin_id = $admin->id;
             $new_track->title = $title;
             $new_track->audio_type = TRACK_TYPE_AUDIO;

             $upload = new Upload($file);
             $upload->set_max_size(MAX_AUDIO_SIZE);
             $upload->set_type(AUDIO_FILE);
             $upload->set_folder(ADMIN_UPLOAD_FOLDER);
             
             if($upload->upload()) {
                 $new_track->track_name = $upload->get_file_name();
                 $new_track->track_duration = $upload->get_duration();

                 $track_image = $upload->get_audio_image();
                 if(!empty($track_image)){
                     $new_track->image_name = $track_image;
                     $new_track->resolution = $upload->get_audio_image_resolution();
                 }


             }

             $errors = $upload->get_errors();
             if($errors->is_empty()) {

                 $track_form_db = new Track();
                 $track_form_db = $track_form_db->where(["id" => $id])->one();



                 if($new_track->where(["id" => $id])->update()){

                     Upload::delete(ADMIN_UPLOAD_FOLDER, $track_form_db->track_name, AUDIO_FILE);
                     if(!empty($track_form_db->image_name) && $track_image != null) {
                         Upload::delete(ADMIN_UPLOAD_FOLDER, $track_form_db->image_name);
                     }

                     $response_obj["track"]["radio"]["audio_type"] = TRACK_TYPE_AUDIO;
                     $response_obj["track"]["audio"]["track_name"] = $new_track->track_name;
                     $response_obj["track"]["image"]["image_name"] = $new_track->image_name;
                     $response_obj["track"]["text"] = ["id" => $new_track->id];

                     $response->create(200, "Success", $response_obj);

                 }else $response->create(201, "Something went wrong. Please try again.", null);
             }else $response->create(201, $errors->get_error_str(), null);

         } else if($file){

             $new_track = new Track();
             $new_track->admin_id = $admin->id;
             $new_track->title = $title;
             $new_track->audio_type = TRACK_TYPE_AUDIO;

             $upload = new Upload($file);
             $upload->set_folder(ADMIN_UPLOAD_FOLDER);
             $upload->set_max_size(MAX_AUDIO_SIZE);
             $upload->set_type(AUDIO_FILE);

             if($upload->upload()) {
                 $new_track->track_name = $upload->get_file_name();
                 $new_track->track_duration = $upload->get_duration();

                 $new_track->image_name = $upload->get_audio_image();
                 $new_track->resolution = $upload->get_audio_image_resolution();
             }

             $errors = $upload->get_errors();
             if($errors->is_empty()) {

                 $new_track->created = date(DATE_FORMAT);
                 $new_track->id = $new_track->save(["image_name", "resolution"]);
                 if($new_track->id > 0){

                     $response_obj["track"]["audio"]["track_name"] = $new_track->track_name;
                     $response_obj["redirect"] = "id=" . $new_track->id;
                     $response_obj["track"]["image"] = ["image_name" => $new_track->image_name];
                     $response_obj["track"]["text"] = ["id" => $new_track->id];
                     $response_obj["track"]["title"] = ["id" => $new_track->title];
                     $response_obj["track"]["radio"]["audio_type"] = TRACK_TYPE_AUDIO;
                     $response->create(200, "Success", $response_obj);

                 }else $response->create(201, "Something went wrong. please try again.", null);
             }else $response->create(201, $errors->get_error_str(), null);

         }else $response->create(201, "Invalid File", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>