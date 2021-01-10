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

        $total_genre = new Genre();

        $has_search = ($search && ($search != "") && ($search != null));
        $has_sort = ($sort && ($sort != "") && ($sort != null));

        if($has_search){
            $total_genre = $total_genre->where(['admin_id' => $admin->id])->like(["title" => $search])->search()->count();
        } else {
            $total_genre = $total_genre->where(['admin_id' => $admin->id])->count();
        }

        $response_values = [];

        $response_obj["total"] = $total_genre;
        $response_obj["page"] = $page;
        $response_obj["page_item"] = BACKEND_PAGINATION;

        $genres = new Genre();

        if($has_search && $has_sort){
            $genres = $genres->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->like(["title" => $search])->search()->orderBy($sort)->orderType($sort_type)->all();

        }else if($has_sort){
            $genres = $genres->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->orderBy($sort)->orderType($sort_type)->all();

        }else if($has_search){
            $genres = $genres->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->like(["title" => $search])->search()
                ->orderBy("created")->orderType("DESC")
                ->all();

        } else {
            $genres = $genres->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->orderBy("created")->orderType("DESC")
                ->all();
        }

        $response_obj["current_item_count"] = count($genres);
        
        foreach ($genres as $item){

            $track_count = new Track();
            $track_count = $track_count->where(['admin_id' => $admin->id])->like(["genres" => "," . $item->id . ","])->search()->count();
            
            $current_genre["delete"] = $item->id;
            $current_genre["edit"] = $item->id;

            $current_values["title"]["text"] = $item->title;
            $current_values["title"]["link"] = "genre-tracks.php?genre_id=" . $item->id;

            $current_values["tracks"]["text"] = $track_count;

            $current_values["created"]["text"] = Helper::days_ago($item->created);

            $current_genre["values"] = $current_values;

            array_push($response_values, $current_genre);
        }

        $response_obj["head"] = ["Title" => [ "title", "DESC" ],
            "Tracks" => null, "Created" => [ "created", "ASC" ], "" => null ];
        $response_obj["body"] = $response_values;

        $response->create(200, "Success", $response_obj);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>