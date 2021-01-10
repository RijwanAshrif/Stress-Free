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

            $genre_id = Helper::post_val("genre_id");
            if($genre_id){

                $user_id = Helper::post_val("user_id");
                $page = Helper::post_val("page");

                $tracks = new Track();

                $genre_id = ',' . $genre_id . ',';

                if($page) {
                    $start = ($page - 1) * API_PAGINATION;
                    $tracks = $tracks->where(["admin_id" => $admin_token_db->admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                        ->like(["genres" => $genre_id])->search()
                        ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all();
                }else{
                    $tracks = $tracks->where(["admin_id" => $admin_token_db->admin_id])->andWhere(["status"=>STATUS_ACTIVE])
                        ->like(["genres" => $genre_id])->search()
                        ->orderBy("created")->orderType("DESC")->all();
                }

                $response_obj = [];
                foreach ($tracks as $item){

                    array_push($response_obj, Helper::format_track($item, $user_id));

                }

                $response->create(200, "Success", $response_obj);
                
            }else $response->create(201, "Invalid Parameter", null);
        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>