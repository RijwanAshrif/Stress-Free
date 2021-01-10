<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $albums = new Album();
        $albums = $albums->where(['admin_id' => $admin->id])->andWhere(["status" => STATUS_ACTIVE])
            ->orderBy("title")->orderType("ASC")
            ->all("id, title, image_name");

        $response->create(200, "Success", $albums);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>