<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){
        $playlist_id = Helper::get_val("playlist_id");

        if($playlist_id){

            $page = Helper::get_val("page");
            if(!$page) $page = 1;

            $start = ($page - 1) * BACKEND_PAGINATION;

            $search = Helper::get_val("search");
            $sort = Helper::get_val("sort");
            $sort_type = Helper::get_val("sort_type");

            if(!$sort_type) $sort_type = "DESC";

            $has_search = ($search && ($search != "") && ($search != null));
            $has_sort = ($sort && ($sort != "") && ($sort != null));

            $joined_sql  = " FROM track as t INNER JOIN playlist_track as pt ON t.id=pt.track_id ";
            $joined_sql .= " WHERE admin_id=1 AND playlist_id=" . $playlist_id;
            

            if($has_search) $count_sql = $joined_sql . " AND t.title LIKE '%" . $search . "%'";
            else $count_sql = $joined_sql;


            $total_track = new Track();
            $total_track = $total_track->set_sql($count_sql)->count();


            $response_values = [];

            $response_obj["total"] = $total_track;
            $response_obj["page"] = $page;
            $response_obj["page_item"] = BACKEND_PAGINATION;

            $tracks = new Track();

            $value_sql = " t.title, t.id, t.album, t.artists, t.status, pt.track_id, pt.playlist_id, pt.created " . $joined_sql;

            if($has_search && $has_sort){

                $value_sql .= " AND t.title LIKE '%" . $search . "%'";
                $value_sql .= " ORDER BY " . $sort . " " . $sort_type;

            }else if($has_sort){
                $value_sql .= " ORDER BY " . $sort . " " . $sort_type;

            }else if($has_search){
                $value_sql .= " AND t.title LIKE '%" . $search . "%'";
                $value_sql .= " ORDER BY " . "CREATED " . " " . $sort_type;

            } else {
                $value_sql .= " ORDER BY " . "CREATED " . " " . $sort_type;
            }


            $value_sql .= " LIMIT " . $start . ", " . BACKEND_PAGINATION;

            $tracks = new Track();
            $tracks = $tracks->set_sql($value_sql)->all();


            $response_obj = Helper::track_table($tracks);

            $response->create(200, "Success", $response_obj);

        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>