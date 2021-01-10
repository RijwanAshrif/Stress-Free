<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $search = Helper::get_val("search");
        $response_obj = [];

        if($search) {
            $tracks = new Track();

            $tracks = $tracks->where(['admin_id' => $admin->id])
                ->like(["title" => $search])->search()
                ->orderBy("created")->orderType("DESC")
                ->all();
            
            foreach ($tracks as $item) {
                array_push($response_obj, Helper::format_tabbed_track($item));
            }
        }

        $response->create(200, "Success", $response_obj);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>