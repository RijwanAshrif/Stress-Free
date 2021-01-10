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

            $genres = new Genre();

            if($page) {
                $start = ($page - 1) * API_PAGINATION;
                $genres = $genres->where(["admin_id" => $admin_token_db->admin_id])
                    ->limit($start, API_PAGINATION)->orderBy("created")->orderType("DESC")->all("id, title");
            }else{
                $genres = $genres->where(["admin_id" => $admin_token_db->admin_id])
                    ->orderBy("created")->orderType("DESC")->all("id, title");
            }
            
            $genre_arr = [];

            foreach ($genres as $item) {
                array_push($genre_arr, Helper::format_genre($item));
            }
            
            $response->create(200, "Success", $genre_arr);

        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();



?>