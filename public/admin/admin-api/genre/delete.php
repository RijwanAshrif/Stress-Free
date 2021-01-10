<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");

        if($id){
            $genre = new Genre();
            $genre = $genre->where(["id" => $id])->one("admin_id");
            
            if(!empty($genre)){
                if($genre->admin_id == $admin->id){

                    $delete_genre = new Genre();
                    if($delete_genre->where(["id" => $id])->delete()){

                        $response->create(200, "Success", $genre);

                    }else $response->create(201, "Something went wrong. Please try again.", null);
                }else $response->create(201, "You are unable to delete this genre", null);
            }else $response->create(201, "Invalid Genre", null);
        }else $response->create(201, "Invalid Genre", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>