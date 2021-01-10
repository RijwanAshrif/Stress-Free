<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());



function get_size_from_header( $data ) {

    if( $data ) {
        $content_length = "unknown";
        $status = "unknown";

        if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
            $status = (int)$matches[1];
        }

        if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
            $content_length = (int)$matches[1];
        }

        // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
        if( $status == 200 || ($status > 300 && $status <= 308) ) {
            $result = $content_length;
        }
    }

    return $result;
}


function get_audio_duration($link){
    $SongPath = $link;

    if($SongPath){
        $filename = tempnam('/tmp','getid3');
        $req_file = file_get_contents($SongPath, false, null, 0, 30000);

        $file_size = get_size_from_header(implode( ", ",  $http_response_header ));

        if (is_numeric($file_size) && file_put_contents($filename, $req_file)) {

            $getID3 = new getID3;
            $ThisFileInfo = $getID3->analyze($filename);
            
            unlink($filename);
        }
    }
}



if(!empty($admin)) {
    if(Helper::is_post()){

        $audio_link = Helper::post_val("audio_link");
        $audio_duration = Helper::post_val("duration");
        $id = Helper::post_val("id");
        $album_id = Helper::post_val("album_id");
        $artist_id = Helper::post_val("artist_id");
        $track = new Track();
        $success = false;
        $track->remote_duration = $audio_duration;

        if($audio_link){
            $track->audio_type = TRACK_TYPE_YOUTUBE;
            $track->audio_link = $audio_link;


            $filename = basename($audio_link);

            foreach (SUPPORTED_AUDIO as $i){
                if(strpos($filename, $i) !== false)  $track->title = str_replace('.' . $i, '', $filename);
            }


            $response_obj["track"]["text"]["audio_link"] = $track->audio_link;
            $response_obj["track"]["text"]["title"] = $track->title;
            $response_obj["track"]["radio"]["audio_type"] = TRACK_TYPE_YOUTUBE;

            if($album_id) {
                $album = new Album();
                $album = $album->where(["id" => $album_id])->one("genres");

                $track->genres = $album->genres;
                $track->album = $album_id;
                $response_obj["redirect"] = "id=" . $album_id;
            }else if($artist_id) {
                $artist = new Artist();
                $artist = $artist->where(["id" => $artist_id])->one("genres");

                $track->genres = $artist->genres;
                $track->artists = ',' . $artist_id . ',';
                $response_obj["redirect"] = "id=" . $artist_id;
            }

            if($id){
                if($track->where(["id" =>$id])->update()){
                    $success = true;
                }
            }else{
                $track->admin_id = $admin->id;
                $track->audio_type = TRACK_TYPE_YOUTUBE;
                $track->created = date(DATE_FORMAT);
                $track->status = STATUS_ACTIVE;

                $track->id = $track->save();

                if($track->id > 0) {
                    if(!$album_id && !$artist_id) $response_obj["redirect"] = "id=" . $track->id;
                    else if($album_id){
                        $response_obj["album_page"]["id"] = $track->id;
                        $response_obj["album_page"]["audio_link"] = $track->audio_link;
                        $response_obj["album_page"]["title"] = $track->title;
                        $response_obj["album_page"]["audio_type"] = $track->audio_type;

                    }else if($artist_id){
                        $response_obj["artist_page"]["id"] = $track->id;
                        $response_obj["artist_page"]["audio_link"] = $track->audio_link;
                        $response_obj["artist_page"]["title"] = $track->title;
                        $response_obj["artist_page"]["audio_type"] = $track->audio_type;

                    }
                    $success = true;
                }
            }

            if($success) $response->create(200, "Success", $response_obj);
            else $response->create(201, "Something went wrong. Please try again.", null);

        }else $response->create(201, "Invalid parameter", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>