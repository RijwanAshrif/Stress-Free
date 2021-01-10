<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");

        if($id){
            $track = new Track();
            $track = $track->where(["id" => $id])->one();
            
            if(!empty($track)){
                if($track->admin_id == $admin->id){

                    $delete_track = new Track();
                    if($delete_track->where(["id" => $id])->delete()){

                        if(!empty($track->track_name)) Upload::delete(ADMIN_UPLOAD_FOLDER, $track->track_name, AUDIO_FILE);

                        if(!empty($track->image_name)) Upload::delete(ADMIN_UPLOAD_FOLDER, $track->image_name);

                        $fav = new Favourite();
                        $fav->where(["track_id" => $id])->delete();

                        $playlist_track = new Playlist_Track();
                        $playlist_track->where(["track_id" => $id])->delete();

                        $recently_played = new Recently_Played();
                        $recently_played->where(["track_id" => $id])->delete();

                        $response->create(200, "Success", $track);

                    }else $response->create(201, "Something went wrong. Please try again.", null);
                }else $response->create(201, "You are unable to delete this track", null);
            }else $response->create(201, "Invalid Track", null);
        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>