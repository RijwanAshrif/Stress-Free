<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");

        if($id){
            $artist = new Artist();
            $artist = $artist->where(["id" => $id])->one("image_name, admin_id");
            
            if(!empty($artist)){
                if($artist->admin_id == $admin->id){

                    $delete_artist = new Artist();
                    if($delete_artist->where(["id" => $id])->delete()){

                        Upload::delete(ADMIN_UPLOAD_FOLDER, $artist->image_name);
                        $response->create(200, "Success", $artist);

                    }else $response->create(201, "Something went wrong. Please try again.", null);
                }else $response->create(201, "You are unable to delete this artist", null);
            }else $response->create(201, "Invalid Artist", null);
        }else $response->create(201, "Invalid Artist", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>