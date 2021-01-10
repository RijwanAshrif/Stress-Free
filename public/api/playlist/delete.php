<?php require_once('../../../private/init.php'); ?>

<?php

$response = new Response();
$errors = new Errors();

if(Helper::is_post()){
    $admin_token = Helper::post_val("admin_token");
    $user_token = Helper::post_val("user_token");

    if($admin_token && $user_token){
        $admin_token_db = new Setting();
        $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

        $user_token_db = new User_Token();
        $user_token_db = $user_token_db->where(["user_token" => $user_token])->one();

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
            }else $response->create(201, "Invalid Parameter", null);
            
        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>