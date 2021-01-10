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

            $user_id = Helper::post_val('user_id');
            $page = Helper::post_val('page');

            if($user_id){

                $favourites = new Favourite();

                if($page) {
                    $start = ($page - 1) * API_PAGINATION;
                    $favourites = $favourites->where(["user_id" => $user_id])
                        ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
                }else{
                    $favourites = $favourites->where(["user_id" => $user_id])
                        ->orderBy("created")->orderType("DESC")->all();
                }

                $response_obj = [];
                foreach ($favourites as $item){

                    $track = new Track();
                    $track = $track->where(["id" => $item->track_id])->one();
                    if(!empty($track)) array_push($response_obj, Helper::format_track($track, null, true));
                }

                $response->create(200, "Success", $response_obj);

            }else $response->create(201, "Invalid Parameter", null);
                
        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>