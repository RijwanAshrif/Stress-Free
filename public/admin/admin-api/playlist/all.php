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

        $total_playlist = new Playlist();

        $has_search = ($search && ($search != "") && ($search != null));
        $has_sort = ($sort && ($sort != "") && ($sort != null));


        if($has_search){
            $total_playlist = $total_playlist->where(['admin_id' => $admin->id])->like(["title" => $search])->search()->count();
        } else {
            $total_playlist = $total_playlist->where(['admin_id' => $admin->id])->count();
        }

        $response_values = [];

        $response_obj["total"] = $total_playlist;
        $response_obj["page"] = $page;
        $response_obj["page_item"] = BACKEND_PAGINATION;

        $playlists = new Playlist();

        if($has_search && $has_sort){
            $playlists = $playlists->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->like(["title" => $search])->search()->orderBy($sort)->orderType($sort_type)->all();

        }else if($has_sort){
            $playlists = $playlists->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->orderBy($sort)->orderType($sort_type)->all();

        }else if($has_search){
            $playlists = $playlists->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->like(["title" => $search])->search()
                ->orderBy("created")->orderType("DESC")
                ->all();

        } else {
            $playlists = $playlists->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->orderBy("created")->orderType("DESC")
                ->all();
        }

        $response_obj["current_item_count"] = count($playlists);
        
        foreach ($playlists as $item){
            $playlist_track_count = new Playlist_Track();
            $playlist_track_count = $playlist_track_count->where(["playlist_id" => $item->id])->count();

            $current_track["edit"] = $item->id;
            $current_track["delete"] = $item->id;
            $current_track["status"] = ($item->status == STATUS_ACTIVE) ? true: false;
            $current_track["featured"] = ($item->featured == STATUS_ACTIVE) ? true: false;
            $current_track["image"] = !empty($item->image_name) ? $item->image_name : '';

            $current_values["title"]["text"] = $item->title;

            if($item->user_id < 0) $created_by = "Admin";
            else{
                $user = new User();
                $user = $user->where(["id" => $item->user_id])->one();

                if(!empty($user)) $created_by = $user->username;
                else $created_by = "Undefined";
            }

            $current_values["created_by"]["text"] = $created_by;
            $current_values["tracks"]["text"] = $playlist_track_count;
            $current_values["title"]["link"] = "playlist-tracks.php?playlist_id=" . $item->id;
            $current_values["created"]["text"] = Helper::days_ago($item->created);

            $current_track["values"] = $current_values;

            array_push($response_values, $current_track);
        }

        $response_obj["head"] = ["Image" => null, "Title" => [ "title", "DESC" ], "Created by" => [ "user_id", "DESC" ],
            "Tracks" => null, "Created" => [ "created", "ASC" ],
            "Status" => [ "status", "DESC" ], "Featured" => [ "featured", "DESC" ], "" => null ];
        $response_obj["body"] = $response_values;

        $response->create(200, "Success", $response_obj);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>