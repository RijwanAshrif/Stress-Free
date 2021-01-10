<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();
$admin = Session::get_session(new Admin());

if(!empty($admin)) {
    if(Helper::is_get()){

        $page = Helper::get_val("page");
        if(!$page) $page = 1;

        $start = ($page - 1) * BACKEND_PAGINATION;

        $search = Helper::get_val("search");
        $sort = Helper::get_val("sort");
        $sort_type = Helper::get_val("sort_type");

        if(!$sort_type) $sort_type = "DESC";

        $total_user = new User();

        $has_search = ($search && ($search != "") && ($search != null));
        $has_sort = ($sort && ($sort != "") && ($sort != null));


        if($has_search){
            $total_user = $total_user->where(['admin_id' => $admin->id])
                ->like(["email" => $search])->like(["username" => $search])->search()->count();
        } else {
            $total_user = $total_user->where(['admin_id' => $admin->id])->count();
        }

        $response_values = [];

        $response_obj["total"] = $total_user;
        $response_obj["page"] = $page;
        $response_obj["page_item"] = BACKEND_PAGINATION;

        $users = new User();

        if($has_search && $has_sort){
            $users = $users->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->like(["email" => $search])->like(["username" => $search])->search()
                ->orderBy($sort)->orderType($sort_type)->all();

        }else if($has_sort){
            $users = $users->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->orderBy($sort)->orderType($sort_type)->all();

        }else if($has_search){
            $users = $users->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->like(["email" => $search])->like(["username" => $search])->search()
                ->orderBy("created")->orderType("DESC")
                ->all();

        } else {
            $users = $users->where(['admin_id' => $admin->id])->limit($start, BACKEND_PAGINATION)
                ->orderBy("created")->orderType("DESC")
                ->all();
        }

        $response_obj["current_item_count"] = count($users);


        foreach ($users as $item){

            $logged_with = "Undefined";
            if($item->type == USER_TYPE_EMAIL) $logged_with = "Email";
            else if($item->type == USER_TYPE_FACEBOOK) $logged_with = "Facebook";
            else if($item->type == USER_TYPE_GMAIL) $logged_with = "Gmail";

            $current_gender = "N/A";
            if($item->gender == GENDER_TYPE_MALE) $current_gender = "Male";
            else if($item->gender == GENDER_TYPE_FEMALE) $current_gender = "Female";

            $current_values["username"]["text"]  = $item->username;
            $current_values["type"]["text"] = $logged_with;
            $current_values["email"]["text"] = (!empty($item->email)) ? $item->email : "N/A";
            $current_values["gender"]["text"] = $current_gender;
            $current_values["created"]["text"] = Helper::days_ago($item->created);

            $current_item["delete"] = $item->id;
            
            $current_item["image"] = (!empty($item->image_name)) ? $item->image_name : DEFAULT_IMAGE;


            $current_item["values"] = $current_values;

            array_push($response_values, $current_item);
        }

        $response_obj["head"] = ["Image" => null, "Logged with" => [ "email", "DESC"],
            "Username" => [ "username", "DESC" ], "Email" => [ "email", "DESC"],
            "Gender" => [ "gender", "DESC"],
            "Created" => [ "created", "ASC" ], "" => null ];
        $response_obj["body"] = $response_values;

        $response->create(200, "Success", $response_obj);

    }else $response->create(201, "Invalid Request Method", null);
}else $response->create(201, "Please log in", null);

echo $response->print_response();

?>