<?php require_once('../../../../private/init.php'); ?>

<?php
$response = new Response();

if (Helper::is_post()) {
    $admin = new Admin();
    $email = Helper::post_val("email");
    $password = Helper::post_val("password");

    if($email & $password){
        $admin = new Admin();
        $admin = $admin->where(["email" => $email])->one();

        if(!empty($admin)){
            if(password_verify($password, $admin->password)){

                $response_admin= new Admin();
                $response_admin->id = (int) $admin->id;
                $response_admin->email = $admin->email;
                $response_admin->username = $admin->username;

                Session::set_session($response_admin);

                $response_admin->create_property("redirect",  "id=" . $response_admin->id);

                $response->create(200, "Success", $response_admin->to_valid_array());

            }else $response->create(201, "Invalid Username/Password", null);
        }else $response->create(201, "Invalid Username/Password", null);
    }else $response->create(201, "Invalid Parameter", null);
}else $response->create(201, "Invalid Parameter", null);

echo $response->print_response();

?>