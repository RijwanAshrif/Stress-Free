<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $page = Helper::get_val("page");
        if(!$page) $page = 1;

        $start = ($page - 1) * BACKEND_PAGINATION;

        $search = Helper::get_val("search");
        $sort = Helper::get_val("sort");
        $sort_type = Helper::get_val("sort_type");

        if(!$sort_type) $sort_type = "DESC";

        $total_artist = new Artist();

        $has_search = ($search && ($search != "") && ($search != null));
        $has_sort = ($sort && ($sort != "") && ($sort != null));


        if($has_search){
            $total_artist = $total_artist->where(['admin_id' => $admin->id])->like(["name" => $search])->search()->count();
        } else {
            $total_artist = $total_artist->where(['admin_id' => $admin->id])->count();
        }

        $response_values = [];

        $response_obj["total"] = $total_artist;
        $response_obj["page"] = $page;
        $response_obj["page_item"] = BACKEND_PAGINATION;

        $artists = new Artist();

        if($has_search && $has_sort){
            $artists = $artists->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->like(["name" => $search])->search()->orderBy($sort)->orderType($sort_type)->all();

        }else if($has_sort){
            $artists = $artists->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->orderBy($sort)->orderType($sort_type)->all();

        }else if($has_search){
            $artists = $artists->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->like(["name" => $search])->search()
                ->orderBy("created")->orderType("DESC")
                ->all();

        } else {
            $artists = $artists->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->orderBy("created")->orderType("DESC")
                ->all();
        }

        $response_obj["current_item_count"] = count($artists);


        foreach ($artists as $item){

            $track_count = new Track();
            $track_count = $track_count->where(['admin_id' => $admin->id])->like(["artists" => "," . $item->id . ","])->search()->count();

            $current_artist["status"] = ($item->status == STATUS_ACTIVE) ? true: false;
            $current_artist["delete"] = $item->id;
            $current_artist["edit"] = $item->id;
            $current_artist["image"] = $item->image_name;

            $current_values["name"]["text"] = ($item->name != null) ? $item->name : 'Undefined';
            $current_values["name"]["link"] = "artist-tracks.php?artist_id=" . $item->id;

            $current_values["tracks"]["text"] = $track_count;
            $current_values["tracks"]["link"] = null;

            $current_values["created"]["text"] = Helper::days_ago($item->created);
            
            $current_artist["values"] = $current_values;

            array_push($response_values, $current_artist);
        }

        $response_obj["head"] = ["Image" => null, "Name" => [ "name", "DESC" ],
            "Tracks" => null, "Created" => [ "created", "ASC" ], "Status" => [ "status", "DESC" ], "" => null ];
        $response_obj["body"] = $response_values;

        $response->create(200, "Success", $response_obj);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>