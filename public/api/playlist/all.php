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
            $page = Helper::post_val("page");
            $user_id = Helper::post_val("user_id");
            $all_playlist = new Playlist();

            if($page) {
                $start = ($page - 1) * API_PAGINATION;
                $all_playlist = $all_playlist->where(["admin_id" => $admin_token_db->admin_id])
                    ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all("id, title, user_id, image_name, resolution");
            }else{
                $all_playlist = $all_playlist->where(["admin_id" => $admin_token_db->admin_id])
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


                $item->title = Helper::decode_entity($item->title);
                array_push($playlist_arr, Helper::format_image($item));
            }

            $response->create(200, "Success", $playlist_arr);

        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>