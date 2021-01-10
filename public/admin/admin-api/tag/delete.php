<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $id = Helper::get_val("id");

        if($id){
            $tag = new Tag();
            $tag = $tag->where(["id" => $id])->one("admin_id");
            
            if(!empty($tag)){
                if($tag->admin_id == $admin->id){

                    $delete_tag = new Tag();
                    if($delete_tag->where(["id" => $id])->delete()){

                        $response->create(200, "Success", $tag);

                    }else $response->create(201, "Something went wrong. Please try again.", null);
                }else $response->create(201, "You are unable to delete this tag", null);
            }else $response->create(201, "Invalid Tag", null);
        }else $response->create(201, "Invalid Tag", null);
    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>