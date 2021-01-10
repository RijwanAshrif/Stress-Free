<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $album_id = Helper::get_val("album_id");

        if($album_id){
            $tracks = new Track();
            $tracks = $tracks->where(['admin_id' => $admin->id])->andWhere(["album" => $album_id])
                ->orderBy("created")->orderType("ASC")
                ->all();
        }

        $tracks_arr = [];

        if(!empty($tracks)){
            foreach ($tracks as $item){

                $response_track["value"] = Helper::format_tabbed_track($item);
                $response_track["delete"] = $item->id;;
                $response_track["edit"] = $item->id;;

                array_push($tracks_arr, $response_track);
            }
        }

        $response_obj["album"]["multiple_tracks"] = $tracks_arr;
        $response->create(200, "Success", $response_obj);


    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>