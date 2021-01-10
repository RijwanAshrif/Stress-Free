<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_post()){

        $title = Helper::post_val("title");

        if($title){
            $tag = new Tag();
            $tag->title = $title;
            $tag->admin_id = $admin->id;
            $tag->created = date(DATE_FORMAT);

            $tag->id = $tag->save();

            if(!empty($tag->id)) $response->create(200, "Success", $tag);
            else $response->create(201, "Something went wrong.", null);

        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>