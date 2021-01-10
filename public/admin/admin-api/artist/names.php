<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $artists= new Artist();
        $artists = $artists->where(['admin_id' => $admin->id])->andWhere(["status" => STATUS_ACTIVE])
            ->orderBy("created")->orderType("DESC")
            ->all("id, name");

        if(!empty($artists)){

            $response_obj = [];
            foreach ($artists as $item){
                $response_obj[$item->id] = $item->name;
            }
            
            $response->create(200, "Success", $response_obj);

        }else $response->create(201, "No data found.", null);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>