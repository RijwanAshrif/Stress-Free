<?php require_once('../../../../private/init.php'); ?>

<?php

function get_youtube_detail($youtube_id){
    $url = YOUTUBE_DETAIL_API . $youtube_id . YOUTUBE_API_FIELDS;
    $youtube_response = json_decode(Helper::curl_get($url), true);

    $response_obj["title"] = $youtube_response["items"][0]["snippet"]["title"];
    $response_obj["duration"] = $youtube_response["items"][0]["contentDetails"]["duration"];
    
    $response = new Response();
   $response->create(200, "Success", $response_obj);
    return $response;
}


function convert_str_to_response($str){
    $res_frm_api = json_decode($str);
    $response = new Response();
    $response->create($res_frm_api->status_code, $res_frm_api->message, $res_frm_api->data);
    return $response;
}

if(Helper::is_post()) {
    $action = Helper::post_val("action");
    $response = new Response();
    $response->create(201, "Something Went Missing", null);



    if($action){
        switch($action){
            
            case ACTION_YOUTUBE_DETAILS:
                $youtube_id = Helper::post_val("youtube_id");
                
                if($youtube_id) $response = get_youtube_detail($youtube_id);
                break;

            default:
                $response->create(201, "Something Went Missing", null);
                break;
        }

    }else $response->create(201, "Something Went Missing", null);

    echo $response->print_response();
}





?>