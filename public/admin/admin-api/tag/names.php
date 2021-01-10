<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $tags = new Tag();
        $tags = $tags->where(['admin_id' => $admin->id])->orderBy("created")->orderType("DESC")->all("id, title");

        if(!empty($tags)){

            $response_obj = [];
            foreach ($tags as $item){
                $response_obj[$item->id] = $item->title;
            }
            
            $response->create(200, "Success", $response_obj);

        }else $response->create(201, "No data found.", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>