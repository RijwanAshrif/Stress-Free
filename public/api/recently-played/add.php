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
            $track_id = Helper::post_val('track_id');

            if($track_id && $user_id){

                $recently_played = new Recently_Played();
                $recently_played->user_id = $user_id;
                $recently_played->track_id = $track_id;
                $recently_played->created = date(DATE_FORMAT);

                $recently_played_by_user = new Recently_Played();
                $recently_played_by_user = $recently_played_by_user->where(["user_id" => $user_id])
                    ->orderBy("created")->orderType("DESC")->all();

                $success = false;
                if(count($recently_played_by_user) >= TRACK_HISTORY){

                    $lastItem = $recently_played_by_user[count($recently_played_by_user) - 1];
                    if($recently_played->where(["id" => $lastItem->id])->update()) {
                        $success = true;
                        $recently_played->id = $lastItem->id;
                    }

                }else{
                    $recently_played->id = $recently_played->save();
                    if($recently_played->id > 0) $success = true;
                }

                if($success) $response->create(200, "Success", $recently_played);
                else $response->create(201, "Something went wrong. Please try again", null);
                    
            }else $response->create(201, "Invalid Parameter", null);
                
        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>