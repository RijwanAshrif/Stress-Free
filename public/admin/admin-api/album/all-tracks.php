<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $album_id = Helper::get_val("album_id");


        $page = Helper::get_val("page");
        if(!$page) $page = 1;

        $start = ($page - 1) * BACKEND_PAGINATION;

        $search = Helper::get_val("search");
        $sort = Helper::get_val("sort");
        $sort_type = Helper::get_val("sort_type");

        if(!$sort_type) $sort_type = "DESC";

        $total_track = new Track();

        $has_search = ($search && ($search != "") && ($search != null));
        $has_sort = ($sort && ($sort != "") && ($sort != null));


        if(!$album_id){

            if($has_search){
                $total_track = $total_track->where(['admin_id' => $admin->id])
                    ->andNull("album")
                    ->like(["title" => $search])->search()->count();
            } else {
                $total_track = $total_track->where(['admin_id' => $admin->id])
                    ->andNull("album")->count();
            }

            $response_values = [];

            $response_obj["total"] = $total_track;
            $response_obj["page"] = $page;
            $response_obj["page_item"] = BACKEND_PAGINATION;

            $tracks = new Track();

            if($has_search && $has_sort){
                $tracks = $tracks->where(['admin_id' => $admin->id])
                    ->andNull("album")
                    ->limit($start, BACKEND_PAGINATION)
                    ->like(["title" => $search])->search()->orderBy($sort)->orderType($sort_type)->all();

            }else if($has_sort){
                $tracks = $tracks->where(['admin_id' => $admin->id])
                    ->andNull("album")
                    ->limit($start, BACKEND_PAGINATION)
                    ->orderBy($sort)->orderType($sort_type)->all();

            }else if($has_search){
                $tracks = $tracks->where(['admin_id' => $admin->id])
                    ->andNull("album")
                    ->limit($start, BACKEND_PAGINATION)
                    ->like(["title" => $search])->search()
                    ->orderBy("created")->orderType("DESC")
                    ->all();

            } else {
                $tracks = $tracks->where(['admin_id' => $admin->id])
                    ->andNull("album")
                    ->limit($start, BACKEND_PAGINATION)
                    ->orderBy("created")->orderType("DESC")
                    ->all();
            }

        } else {

            if($has_search){
                $total_track = $total_track->where(['admin_id' => $admin->id])
                    ->andWhere(["album" => $album_id])
                    ->like(["title" => $search])->search()->count();
            } else {
                $total_track = $total_track->where(['admin_id' => $admin->id])
                    ->andWhere(["album" => $album_id])->count();
            }

            $response_values = [];

            $response_obj["total"] = $total_track;
            $response_obj["page"] = $page;
            $response_obj["page_item"] = BACKEND_PAGINATION;

            $tracks = new Track();

            if($has_search && $has_sort){
                $tracks = $tracks->where(['admin_id' => $admin->id])
                    ->andWhere(["album" => $album_id])
                    ->limit($start, BACKEND_PAGINATION)
                    ->like(["title" => $search])->search()->orderBy($sort)->orderType($sort_type)->all();

            }else if($has_sort){
                $tracks = $tracks->where(['admin_id' => $admin->id])
                    ->andWhere(["album" => $album_id])
                    ->limit($start, BACKEND_PAGINATION)
                    ->orderBy($sort)->orderType($sort_type)->all();

            }else if($has_search){
                $tracks = $tracks->where(['admin_id' => $admin->id])
                    ->andWhere(["album" => $album_id])
                    ->limit($start, BACKEND_PAGINATION)
                    ->like(["title" => $search])->search()
                    ->orderBy("created")->orderType("DESC")
                    ->all();

            } else {
                $tracks = $tracks->where(['admin_id' => $admin->id])
                    ->andWhere(["album" => $album_id])
                    ->limit($start, BACKEND_PAGINATION)
                    ->orderBy("created")->orderType("DESC")
                    ->all();
            }

        }


        $response_obj = Helper::track_table($tracks);

        $response->create(200, "Success", $response_obj);


    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>