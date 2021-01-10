<?php require_once('../../../private/init.php'); ?>

<?php

$response = new Response();
$errors = new Errors();

if(Helper::is_post()){
    $admin_token = Helper::post_val("admin_token");
    $user_token = Helper::post_val("user_token");

    if($admin_token && $user_token){
        $admin_token_db = new Setting();
        $admin_token_db = $admin_token_db->where(["admin_token" => $admin_token])->one();

        $user_token_db = new User_Token();
        $user_token_db = $user_token_db->where(["user_token" => $user_token])->one();

        if(!empty($admin_token_db) && !empty($user_token_db)){

            $searched = Helper::post_val("searched");
            if($searched){

                $search_term_frm_db = new Search_Term();
                $search_term_frm_db = $search_term_frm_db->where(["term" => $searched])->one();

                $search_term = new Search_Term();
                $search_term->created = date(DATE_FORMAT);
                $success = false;

                if(!empty($search_term_frm_db)){
                    $search_term->count = ($search_term_frm_db->count + 1);

                    if($search_term->where(["id" => $search_term_frm_db->id])->update()) {
                        $search_term->term = $search_term_frm_db->term;
                        $search_term->id = $search_term_frm_db->id;
                        $success = true;
                    }
                }else{
                    $search_term->term = $searched;
                    $search_term->admin_id = $admin_token_db->admin_id;
                    $search_term->count = 1;
                    $search_term->id = $search_term->save();
                    if($search_term->id > 0) $success = true;
                }

                if($success) $response->create(200, "Success", $search_term->to_valid_array());
                else $response->create(201, "Invalid Parameter", null);

            }else $response->create(201, "Invalid Parameter", null);
        }else $response->create(201, "Invalid Token Found", null);
    }else $response->create(201, "No Token Found", null);
}else $response->create(201, "Invalid Request Method", null);

echo $response->print_response();


?>