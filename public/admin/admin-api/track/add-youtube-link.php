<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
     if(Helper::is_post()){

         $title = Helper::post_val("title");
         $youtube_link = Helper::post_val("youtube_link");
         $duration = Helper::post_val("duration");
         $id = Helper::post_val("id");
         $album_id = Helper::post_val("album_id");

         if($id && $youtube_link){
             $new_track = new Track();
             $new_track->admin_id = $admin->id;
             $new_track->youtube_link = $youtube_link;
             $new_track->title = $title;
             $new_track->audio_type = TRACK_TYPE_YOUTUBE;
             $new_track->youtube_duration = $duration;

             if($new_track->where(["id" => $id])->update()){

                 $response_obj["track"]["text"]["id"] = $new_track->id;
                 $response_obj["track"]["text"]["youtube_link"] = $new_track->youtube_link;
                 $response_obj["track"]["text"]["youtube_duration"] = $new_track->youtube_duration;
                 $response_obj["track"]["radio"]["audio_type"] = TRACK_TYPE_YOUTUBE;
                 $response->create(200, "Success", $response_obj);

             }else $response->create(201, "Something went wrong. Please try again.", null);


         } else if($youtube_link && $album_id){

             $album = new Album();
             $album = $album->where(["id" => $album_id])->one("artists, genres, tags");

             $new_track = new Track();
             $new_track->admin_id = $admin->id;
             $new_track->title = $title;
             $new_track->audio_type = TRACK_TYPE_YOUTUBE;
             $new_track->youtube_link = $youtube_link;
             $new_track->youtube_duration = $duration;

             $new_track->created = date(DATE_FORMAT);
             $new_track->album = $album_id;
             $new_track->artists = $album->artists;
             $new_track->genres = $album->genres;
             $new_track->tags = $album->tags;
             $new_track->status = STATUS_ACTIVE;

             $new_track->id = $new_track->save();
             if($new_track->id > 0){

                 $response_obj["track"]["text"]["id"] = $new_track->id;
                 $response_obj["track"]["text"]["youtube_link"] = "ss";
                 $response_obj["track"]["text"]["title"] = $new_track->title;
                 $response_obj["track"]["radio"]["audio_type"] = TRACK_TYPE_YOUTUBE;
                 $response->create(200, "Success", $response_obj);

             }else $response->create(201, "Something went wrong. Please try again.", null);

         } else if($youtube_link){

             $new_track = new Track();
             $new_track->admin_id = $admin->id;
             $new_track->title = $title;
             $new_track->audio_type = TRACK_TYPE_YOUTUBE;
             $new_track->youtube_link = $youtube_link;
             $new_track->youtube_duration = $duration;

             $new_track->created = date(DATE_FORMAT);
             $new_track->id = $new_track->save();
             if($new_track->id > 0){

                 $response_obj["redirect"] = "id=" . $new_track->id;
                 $response_obj["track"]["text"]["id"] = $new_track->id;
                 $response_obj["track"]["text"]["youtube_link"] = $new_track->youtube_link;
                 $response_obj["track"]["text"]["title"] = $new_track->title;
                 $response_obj["track"]["radio"]["audio_type"] = TRACK_TYPE_YOUTUBE;
                 $response->create(200, "Success", $response_obj);

             }else $response->create(201, "Something went wrong. Please try again.", null);


         }else $response->create(201, "Invalid File", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>