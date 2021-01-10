<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_post()){

        $title = Helper::post_val("title");

        if($title){
            $genre = new Genre();
            $genre->title = $title;
            $genre->admin_id = $admin->id;
            $genre->created = date(DATE_FORMAT);

            $genre->id = $genre->save();

            if(!empty($genre->id)) $response->create(200, "Success", $genre);
            else $response->create(201, "Something went wrong.", null);

        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>