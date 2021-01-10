<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_post()){
        $track_id = Helper::post_val('track_id');
        $playlist_id = Helper::post_val('playlist_id');

        if($track_id && $playlist_id){

            $response = API_Helper::addToPlayListAdmin($playlist_id, $track_id);
            
        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>