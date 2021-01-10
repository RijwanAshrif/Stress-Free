<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");

        if($id){
            $playlist = new Playlist();
            $playlist = $playlist->where(["id" => $id])->one();
            
            if(!empty($playlist)){
                if($playlist->admin_id == $admin->id){

                    $delete_playlist = new Playlist();
                    if($delete_playlist->where(["id" => $id])->delete()){

                        $playlist_track = new Playlist_Track();
                        $playlist_track->where(["playlist_id" => $id])->delete();

                        $response->create(200, "Success", $playlist);

                    }else $response->create(201, "Something went wrong. Please try again.", null);
                }else $response->create(201, "You are unable to delete this playlist", null);
            }else $response->create(201, "Invalid Playlist", null);
        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>