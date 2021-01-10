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
                    }else $response->create(201, "Invalid Parameter", null);
                }

                if($success) $response->create(200, "Success", $playlist);
                else $response->create(201, "Something went wrong. Please try again", null);
                    
            }else $response->create(201, "Invalid Parameter", null);
                
        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>