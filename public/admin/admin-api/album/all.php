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

        $total_album = new Album();

        $has_search = ($search && ($search != "") && ($search != null));
        $has_sort = ($sort && ($sort != "") && ($sort != null));


        if($has_search){
            $total_album = $total_album->where(['admin_id' => $admin->id])->like(["title" => $search])->search()->count();
        } else {
            $total_album = $total_album->where(['admin_id' => $admin->id])->count();
        }

        $response_values = [];

        $response_obj["total"] = $total_album;
        $response_obj["page"] = $page;
        $response_obj["page_item"] = BACKEND_PAGINATION;

        $albums = new Album();

        if($has_search && $has_sort){
            $albums = $albums->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->like(["title" => $search])->search()->orderBy($sort)->orderType($sort_type)->all();

        }else if($has_sort){
            $albums = $albums->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->orderBy($sort)->orderType($sort_type)->all();

        }else if($has_search){
            $albums = $albums->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->like(["title" => $search])->search()
                ->orderBy("created")->orderType("DESC")
                ->all();

        } else {
            $albums = $albums->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->orderBy("created")->orderType("DESC")
                ->all();
        }

        $response_obj["current_item_count"] = count($albums);

        foreach ($albums as $item){
            
            $track_count = new Track();
            $track_count = $track_count->where(["album" => $item->id])->count();
            
            $current_album["delete"] = $item->id;
            $current_album["edit"] = $item->id;
            $current_album["status"] = ($item->status == STATUS_ACTIVE) ? true: false;
            $current_album["featured"] = ($item->featured == STATUS_ACTIVE) ? true: false;

            $current_album["image"] = $item->image_name;

            $current_values["title"]["text"] = !empty($item->title) ? $item->title : "Undefined";
            $current_values["title"]["link"] = "album-tracks.php?album_id=" . $item->id;

            $current_values["artist"] = Helper::get_artists($item->artists);

            $current_values["track"]["text"] = $track_count;




            $current_values["created"]["text"] = Helper::days_ago($item->created);


            $current_album["values"] = $current_values;

            array_push($response_values, $current_album);
        }

        $response_obj["head"] = ["Image" => null, "Title" => [ "title", "DESC" ],
            "Artist" => [ "artists", "DESC" ], "Tracks" => null,
            "Created" => [ "created", "ASC" ], "Status" => ["status", "DESC"],
            "Featured" => ["featured", "DESC"], "" => null
        ];
        
        $response_obj["body"] = $response_values;

        $response->create(200, "Success", $response_obj);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>