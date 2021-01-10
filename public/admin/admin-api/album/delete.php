<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");

        if($id){
            $album = new Album();
            $album = $album->where(["id" => $id])->one("image_name, admin_id");
            
            if(!empty($album)){
                if($album->admin_id == $admin->id){

                    $delete_album = new Album();
                    if($delete_album->where(["id" => $id])->delete()){

                        Upload::delete(ADMIN_UPLOAD_FOLDER, $album->image_name);
                        $response->create(200, "Success", $album);

                    }else $response->create(201, "Something went wrong. Please try again.", null);
                }else $response->create(201, "You are unable to delete this album", null);
            }else $response->create(201, "Invalid Album", null);
        }else $response->create(201, "Invalid Album", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>