<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");

        if($id){
            $user = new User();
            $user = $user->where(["id" => $id])->one();
            
            if(!empty($user)){
                if($user->admin_id == $admin->id){

                    $delete_user = new User();
                    if($delete_user->where(["id" => $id])->delete()){

                        Upload::delete(ADMIN_UPLOAD_FOLDER, $user->image_name);
                        $response->create(200, "Success", $user);

                    }else $response->create(201, "Something went wrong. Please try again.", null);
                }else $response->create(201, "You are unable to delete this user", null);
            }else $response->create(201, "Invalid User", null);
        }else $response->create(201, "Invalid Parameter", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>