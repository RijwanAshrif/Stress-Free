<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $playlist_id = Helper::get_val("playlist_id");

        if($playlist_id){
            $joined_sql  = " FROM track as t INNER JOIN playlist_track as pt ON t.id=pt.track_id ";
            $joined_sql .= " WHERE admin_id=1 AND playlist_id=" . $playlist_id;

            $value_sql = " t.title, t.id, t.audio_type, t.audio_link, t.remote_duration, t.track_name, t.track_duration, t.id, t.album, t.artists, t.status, pt.track_id, pt.playlist_id, pt.created " . $joined_sql;
            $value_sql .= " ORDER BY " . "CREATED " . " " . "DESC";

            $tracks = new Track();
            $tracks = $tracks->set_sql($value_sql)->all();
        }

        $tracks_arr = [];

        if(!empty($tracks)){
            foreach ($tracks as $item){
                
                $response_track["value"] = Helper::format_tabbed_track($item);
                $response_track["remove_from_pl"] = $item->id;

                array_push($tracks_arr, $response_track);
            }
        }

        $response_obj["playlist"]["multiple_tracks"] = $tracks_arr;
        $response->create(200, "Success", $response_obj);


    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>