<?php


$site_config = Session::get_session(new Site_Config());

if(empty($site_config)){
	$setting = new Setting();
	$setting = $setting->where(["admin_token" => ADMIN_TOKEN])->one();

	$site_config = new Site_Config();
	$site_config = $site_config->where(["admin_id" => $setting->admin_id])->one();
}


$current = basename($_SERVER["SCRIPT_FILENAME"]);

if($current == "index.php") $page_title = $site_config->title . " - " . $site_config->tag_line;
else if(($current == "album.php")) $page_title = "Album";
else if(($current == "artist.php")) $page_title = "Artist";
else if(($current == "download.php")) $page_title = "Download";
else if(($current == "my-music.php")) $page_title = "My Music";
else if(($current == "playlist.php")) $page_title = "Playlist";
else if(($current == "privacy-policy.php")) $page_title = "Privacy Policy";
else if(($current == "profile.php")) $page_title = "Profile";
else if(($current == "tag.php")) $page_title = "Tag";
else if(($current == "about-us.php")) $page_title = "About Us";
else if(($current == "terms-conditions.php")) $page_title = "Terms & Conditions";
else if(($current == "track.php")) $page_title = "Track";




if(empty(Session::get_session(new User_Token()))){
	$user_token = new User_Token();
	$user_token->ip_address = Helper::get_client_ip();
	$user_token->user_token = Helper::unique_code(25);
	$user_token->created = date(DATE_FORMAT);

	$user_token_form_db = new User_Token();
	$user_token_form_db = $user_token_form_db->where(["ip_address" => $user_token->ip_address])->one();

	$success = false;
	if(!empty($user_token_form_db)){
		if($user_token->where(["id" => $user_token_form_db->id])->update()) $success = true;
	}else{
		if($user_token->save()) $success = true;
	}

	$session_user_token = new User_Token();
	$session_user_token->user_token = $user_token->user_token;

	if($success) Session::set_session($session_user_token);
    
}else{

    $user_token = Session::get_session(new User_Token());
    $user_token_form_db = new User_Token();
    $user_token_form_db = $user_token_form_db->where(["user_token" => $user_token->user_token])->one();

    if(empty($user_token_form_db)){

        $user_token = new User_Token();
        $user_token->ip_address = Helper::get_client_ip();
        $user_token->user_token = Helper::unique_code(25);
        $user_token->created = date(DATE_FORMAT);

        $user_token_form_db = new User_Token();
        $user_token_form_db = $user_token_form_db->where(["ip_address" => $user_token->ip_address])->one();

        $success = false;
        if(!empty($user_token_form_db)){
            if($user_token->where(["id" => $user_token_form_db->id])->update()) $success = true;
        }else{
            if($user_token->save()) $success = true;
        }

        $session_user_token = new User_Token();
        $session_user_token->user_token = $user_token->user_token;

        if($success) Session::set_session($session_user_token);
    }
}

$facebook_login_url = '';
$login_button = '';

if(!$logged_in){
    $gClient = new Google_Client();
    $gClient->setClientId(GOOGLE_LOGIN_CLIENT_ID);
    $gClient->setClientSecret(GOOGLE_LOGIN_CLIENT_SECRET);
    $gClient->setRedirectUri(GOOGLE_LOGIN_REDIRECT_URL);

    $gClient->addScope('email');
    $gClient->addScope('profile');

	$facebook = new \Facebook\Facebook([
		'app_id'				=> FACEBOOK_LOGIN_APP_ID,
		'app_secret'			=> FACEBOOK_LOGIN_APP_SECRET,
        'default_graph_version' => 'v3.2',
	]);


	$facebook_helper = $facebook->getRedirectLoginHelper();

    if(isset($_GET["code"])) {
        $google_token = $gClient->fetchAccessTokenWithAuthCode($_GET["code"]);

		if(!empty($google_token) && !isset($google_token['error'])){

            $gClient->setAccessToken($google_token['access_token']);
            $google_service = new Google_Service_Oauth2($gClient);
            $data = $google_service->userinfo->get();
            /*$_SESSION['google_token'] = $gClient->getAccessToken();*/

            Helper::social_login($data, USER_TYPE_GMAIL);

		}else{
            $facebook_token = $facebook_helper->getAccessToken();

            if(!empty($facebook_token)){
                $facebook->setDefaultAccessToken($facebook_token);

                $graph_response = $facebook->get("/me?fields=id,name,email", $facebook_token);
                $facebook_user_info = $graph_response->getGraphUser();
                /*$_SESSION['facebook_token'] = $gClient->getAccessToken();*/

                Helper::social_login($facebook_user_info, USER_TYPE_FACEBOOK);
            }
		}
    }

    if(!isset($_SESSION['google_token'])) {
        $login_button = '<a class="btn-login login-with-google-btn" href="' . $gClient->createAuthUrl().'">Login With Google</a>';
    }

	if(!isset($_SESSION['facebook_token'])) {

		$facebook_login_url = $facebook_helper->getLoginUrl(FACEBOOK_LOGIN_REDIRECT_URL);

		// Render Facebook login button
		$facebook_login_url = '<a class="btn-login login-with-facebook-btn" href="' . $facebook_login_url . '">Login With Facebook</a>';

	}
}





?>


<!DOCTYPE HTML>
<html lang="en">
<head>
	<title><?php echo $page_title; ?></title>
	<link rel="icon" href="<?php echo ADMIN_THUMB_LINK . $site_config->image_name; ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">

	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700%7CLato:400,700%7CRoboto:400,500" rel="stylesheet">

	<!-- Font Icons -->
	<link rel="stylesheet" href="fonts/ionicons.css">

	<!-- Styles -->
	<link rel="stylesheet" href="plugin-frameworks/swiper.min.css">
	<link rel="stylesheet" href="plugin-frameworks/bootstrap-grid.min.css">
	<link rel="stylesheet" href="common/other/styles.css">
</head>

<body>


<div class="track-download-popup" id="track-download-popup">
	<div class="popup-inner">

		<a class="close-btn" href="#" data-close="#track-download-popup"><i class="ion-android-close"></i></a>

		<h5 class="popup-header">

		</h5><!--popup-header-->

		<div class="popup-body">

		</div><!--popup-body-->

	</div><!--popup-inner-->
</div><!--user-audio-player-->



<div class="track-detail-popup" id="track-detail-popup">

    <div class="popup-inner">

		<a class="close-btn" href="#" data-close="#track-detail-popup"><i class="ion-android-close"></i></a>

        <h5 class="popup-header">
			Download
        </h5><!--popup-header-->

        <div class="popup-body">

        </div><!--popup-body-->
        
    </div><!--popup-inner-->
</div><!--user-audio-player-->



<div class="active page-loader btn-loader loader-big"><div class="active ajax-loader"></div></div>

<div class="user-audio-player" id="fixed-bottom-player">
	<a id="player-close-btn" href="#"><i class="ion-close-round"></i></a>
	<div class="fixed-player-inner" >
		<a class="player-detail" href="#">
			<span class="arrow-top-btn" href="#"><i class="ion-android-arrow-dropup"></i></span>
			<img src="images/default-disc.jpg" alt="">
			<p class="title">Song Name</p>
			<p class="artist">John Doe</p>
		</a>
		<div class="player-wrapper">
			<audio controls="" class="track track_name">
				<source src="" type="audio/mpeg">
			</audio>
		</div>
	</div><!--fixed-player-inner-->

	<div id="google-adsence">
        <a href="#"><img src="https://place-hold.it/300x70" alt=""></a>
	</div>

</div><!--user-audio-player-->






<header>
	<div class="container h-100">
		<div class="pos-relative h-100">

			<a class="logo not-load" data-page="home" data-title="Home" href="index.php"><img src="images/logo_black.png" alt=""></a>
			<div class="right-area">
				<ul class="main-menu" id="main-menu">
					<li><a class="not-load" data-page="home" data-title="Home" href="index.php">Home</a></li>
                    <li><a class="not-load" data-page="tracks" data-title="Tracks" href="track.php">Music</a></li>
				

				</ul>

				<a class="toggle-menu-btn" id="hamburger-menu" href="#"><i class="ion-android-menu"></i></a>

				<a class="search-btn" id="search-btn" href="#search-area"><i class="ion-android-search"></i></a>
			</div><!--right-area-->
		</div><!-- pos-relative -->
	</div><!-- container -->
</header>

<div class="search-area" id="search-area">
	<div class="search-inner">

		<a class="close-btn" href="#" data-close="#search-area"><i class="ion-android-close"></i></a>
		<div class="search-wrapper">

			<form class="input-wrapper">
				<input name="search" type="text" placeholder="Search your mood here">
				<button type="submit"><i class="ion-android-search"></i></button>
			</form>

			<div class="link-item" id="popular_search"></div>

			<div class="link-item" id="recent_search"></div>

		</div><!--search-wrapper-->
	</div><!--search-area-->
</div><!--search-area-->


<div class="popup-toast">
	<i class="ion-ios-bell"></i>
	<div class="toast-right">
        <h5 class="title"></h5>
        <h6 class="desc"></h6>

    </div>
</div>
