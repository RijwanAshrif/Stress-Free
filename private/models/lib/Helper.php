<?php

class Helper{

    public static function social_login($data, $type){

        $logged_user = new User();
        $logged_user->type = $type;
        $logged_user->social_id = $data['id'];
        $logged_user->email = isset($data['email']) ? $data['email'] : null;
        $logged_user->username = $data['name'];

        if($type == USER_TYPE_GMAIL) $logged_user->image_name = $data['picture'];
        else if($type == USER_TYPE_FACEBOOK) $logged_user->image_name = "https://graph.facebook.com/" . $logged_user->social_id . "/picture?width=300&height=300";

        $existing_user = new User();
        $existing_user = $existing_user->where(["social_id" => $logged_user->social_id])->one();

        if(empty($existing_user)) {

            $admin_token_db = new Setting();
            $admin_token_db = $admin_token_db->where(["admin_token" => ADMIN_TOKEN])->one();

            $logged_user->admin_id = $admin_token_db->admin_id;

            $logged_user->id = $logged_user->save();
            self::setting_logged_in_user($logged_user);

        }else {
            self::setting_logged_in_user($existing_user);
        }

        if($type == USER_TYPE_GMAIL) header( "Location: " . GOOGLE_LOGIN_REDIRECT_URL);
        else if($type == USER_TYPE_FACEBOOK) header( "Location: " . FACEBOOK_LOGIN_REDIRECT_URL);
    }


    public static function setting_logged_in_user($existing_user){
        $response_user = new User();
        $response_user->type = $existing_user->type;
        $response_user->id = $existing_user->id;
        $response_user->email = $existing_user->email;
        $response_user->username = $existing_user->username;
        Session::set_session($response_user);
    }


    public static function track_table($tracks){
        $response_values = [];
        $response_obj["current_item_count"] = count($tracks);

        foreach ($tracks as $item){
            $current_track["status"] = ($item->status == 1) ? true: false;
            $current_track["lyrics"] = $item->id;
            $current_track["delete"] = $item->id;
            $current_track["edit"] = $item->id;
            $current_track["image"] = $item->image_name;

            $current_values["title"]["text"] = $item->title;
            $current_values["album"] = self::get_album($item->album);
            $current_values["artist"] = self::get_artists($item->artists);
            $current_values["view_count"]["text"] = $item->view_count;
            $current_values["created"]["text"] = Helper::days_ago($item->created);
            $current_track["values"] = $current_values;

            array_push($response_values, $current_track);
        }

        $response_obj["head"] = ["Image" => null, "Title" => [ "title", "DESC" ], "Album" => [ "album", "DESC" ],
            "Artist" => [ "artists", "DESC" ], "Listened by" => [ "view_count", "DESC" ],
            "Created" => [ "created", "ASC" ], "Status" => [ "status", "DESC" ], "" => null];
        $response_obj["body"] = $response_values;

        return $response_obj;
    }


    public static function get_album($album_id){
        if(!empty($album_id)){

            $album = new Album();
            $album = $album->where(["id" => $album_id])->one("id, title");

            if(!empty($album)){

                $current_album["text"] = !empty($album->title) ? $album->title : "Undefined";
                $current_album["link"] = "album-tracks.php?album_id=" . $album_id;
            }else{

                $current_album["text"] = "Single Track";
                $current_album["link"] = "album-tracks.php";
            }
        }else {
            $current_album["text"] = "Single Track";
            $current_album["link"] = "album-tracks.php";
        }


        return $current_album;
    }


    public static function get_artists($artist_all_id){

        $artist_ids = explode(",", $artist_all_id);
        $artists = [];
        foreach ($artist_ids as $inner_item){
            $inner_item = trim($inner_item);
            if(!empty($inner_item)){
                $artist = new Artist();
                $artist = $artist->where(["id" => $inner_item])->one("id, name");

                if(!empty($artist)) {
                    $current_artist["text"] = $artist->name;
                    $current_artist["link"] = "artist-tracks.php?artist_id=" . $artist->id;
                    array_push($artists, $current_artist);
                }
            }
        }

        if(empty($artists)){
            $current_artist["text"] = "Undefined";
            array_push($artists, $current_artist);
        }

        return $artists;
    }


    
    
    public static function format_image($item){
        $image_name = $item->image_name;
        $item->image_name = null;
        $response_item = $item->to_valid_array();

        if(!empty($image_name)){
            $response_item['thumb_link'] = ADMIN_IMAGE_LINK . UPLOADED_THUMB_FOLDER . '/' . $image_name;
            $response_item['image_link'] = ADMIN_IMAGE_LINK . $image_name;
        }else{
            $response_item['thumb_link'] = null;
            $response_item['image_link'] = null;
        }
        
        return $response_item;
    }


    public static function format_track_detail($item, $user_id = null, $favourite = false){
        if($user_id){
            $favourited = new Favourite();
            $favourited = $favourited->where(["track_id" => $item->id])->andWhere(["user_id" => $user_id])->one();
            if(!empty($favourited)) $item->create_property("favourited", 1);
            else $item->create_property("favourited", 2);
        } else{
            if($favourite) $item->create_property("favourited", 1);
            else $item->create_property("favourited", 2);
        }

        $artist_id_arr = explode(",", $item->artists);
        $artist_str = "";
        $artist_arr = [];
        foreach ($artist_id_arr as $inner_item){
            $artist = new Artist();
            $artist = $artist->where(["id" => trim($inner_item)])->one("id, name");

            if(!empty($artist)) {
                $artist_str .= $artist->name . ", ";
                array_push($artist_arr, $artist->to_valid_array());
            }
        }

        $item->create_property("artist_array", $artist_arr);

        $item->artists = substr($artist_str, 0, -2);;

        $audio_link = "";
        $duration  = 0;
        if($item->audio_type == TRACK_TYPE_AUDIO) {
            $audio_link = ADMIN_AUDIO_LINK . '/'. $item->track_name;
            $duration = $item->track_duration;
        } else if($item->audio_type == TRACK_TYPE_YOUTUBE){
            $audio_link = $item->audio_link;
            $duration = $item->remote_duration;
        }

        $item->audio_link = null;
        $item->audio_type = null;
        $item->remote_duration = null;
        $item->track_duration = null;
        $item->track_name = null;
        $item->album = null;
        $item->status = null;
        $item->created = null;

        $response_item = Helper::format_image($item);

        $response_item["encrypted"] = urlencode(Encryption::encrypt(ENCRYPTION_KEY, ENCRYPTION_IV, json_encode(["id" => $item->id])));
        $response_item['audio_link'] = $audio_link;
        $response_item['audio_duration'] = $duration;

        return $response_item;
    }


    public static function format_tabbed_track($item){
        $current_values["id"] = $item->id;
        $current_values["title"] = $item->title;
        $current_values["audio_type"] = $item->audio_type;

        if($item->audio_type == TRACK_TYPE_YOUTUBE){
            $current_values["audio_link"] = $item->audio_link;

        }else if($item->audio_type == TRACK_TYPE_AUDIO){
            $current_values["track_name"] = $item->track_name;
            $current_values["duration"] = $item->track_duration;
        }

        return $current_values;
    }


    public static function decode_entity($str){
        return html_entity_decode($str, ENT_QUOTES | ENT_HTML401, "UTF-8");
    }


    public static function format_tag($item){
        $item->title = self::decode_entity($item);
        return $item;
    }

    public static function format_genre($item){
        $item->title = self::decode_entity($item->title);
        return $item;
    }



    public static function format_track($item, $user_id = null, $favourite = false){
        if($user_id){
            $favourited = new Favourite();
            $favourited = $favourited->where(["track_id" => $item->id])->andWhere(["user_id" => $user_id])->one();
            if(!empty($favourited)) $item->create_property("favourited", 1);
            else $item->create_property("favourited", 2);
        } else{
            if($favourite) $item->create_property("favourited", 1);
            else $item->create_property("favourited", 2);
        }

        $artist_id_arr = explode(",", $item->artists);
        $artist_str = "";
        $artist_arr = [];

        $item->title = self::decode_entity($item->title);

        foreach ($artist_id_arr as $inner_item){
            $artist = new Artist();
            $artist = $artist->where(["id" => trim($inner_item)])->one("id, name");


            if(!empty($artist)) {
                $artist_str .= $artist->name . ", ";
                $single_artist_arr["id"] = $artist->id;
                $single_artist_arr["name"] = self::decode_entity($artist->name);

                array_push($artist_arr, $single_artist_arr);
            }
        }

        $item->create_property("encrypted", urlencode(Encryption::encrypt(ENCRYPTION_KEY, ENCRYPTION_IV, json_encode(["id" => $item->id]))));
        $item->create_property("artist_array", $artist_arr);

        $item->artists = substr($artist_str, 0, -2);;

        $audio_link = "";
        $duration  = 0;
        if($item->audio_type == TRACK_TYPE_AUDIO) {
            $audio_link = ADMIN_AUDIO_LINK . '/'. $item->track_name;
            $duration = $item->track_duration;
        } else if($item->audio_type == TRACK_TYPE_YOUTUBE){
            $audio_link = $item->audio_link;
            $duration = $item->remote_duration;
        }

        $item->audio_link = null;
        $item->audio_type = null;
        $item->remote_duration = null;
        $item->track_duration = null;
        $item->track_name = null;
        $item->tags = null;
        $item->album = null;
        $item->lyrics = null;
        $item->genres = null;
        $item->description = null;
        $item->status = null;
        $item->created = null;

        $response_item = Helper::format_image($item);


        $response_item['audio_link'] = $audio_link;
        $response_item['audio_duration'] = $duration;

        return $response_item;
    }


    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML401, "UTF-8");
    }
    
    public static function arrayToObject(array $array, $className) {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($className),
            $className,
            strstr(serialize($array), ':')
        ));
    }

    public static function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    
    
    public static function get_distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000){
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
    
    public static function objectToObject($instance, $className) {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($className),
            $className,
            strstr(strstr(serialize($instance), '"'), ':')
        ));
    }
    
    public static function redirect_to($url){
        header('Location: ' . $url);
    }

    public static function server_root(){
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
        return $protocol . $_SERVER['SERVER_NAME'];
    }

    public static function is_post(){
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function is_get(){
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    public static function post_val($val){
        return (isset($_POST[$val]) && (!empty($_POST[$val]))) ? Helper::escape(trim($_POST[$val])) : null;
    }

    public static function file_val($val){
        return (isset($_FILES[$val]) && (!empty($_FILES[$val]))) ? $_FILES[$val] : null;
    }
    
    public static function get_val($val){
        return (isset($_GET[$val]) && (!empty($_GET[$val]))) ? Helper::escape(trim($_GET[$val])) : null;
    }

    public static function validateEmail($email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Invalid Email";
        else return null;
    }

    public static function invalid_length($key, $value, $length){
        if(strlen($value) < $length) return ucfirst($key) . ' must be at least ' . $length . ' char long.';
        else return null;
    }

    public static function unique_code($limit){
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }


    function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-128-CBC";
        $secret_key = 'KEY';
        $secret_iv = 'IV';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 8);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
    

    public static function unique_numeric_code($limit){
        return substr(uniqid(mt_rand()), 0, $limit);
    }

    public static function format_address($address){
        $formatted_address = "";
        if(empty($address)) $formatted_address = "Unknown";
        else{
            $address->id = null;
            $address->user = null;
            foreach ($address as $key => $value){
                if(!empty($value)) {
                    $formatted_address .=  $value . ", ";
                }
            }
        }
        return rtrim($formatted_address, ", ");
    }

    public static function time_difference($date){
        $date1 = new DateTime($date);
        $date2 = new DateTime(date(DATE_FORMAT));

        return $date1->diff($date2);
    }


    public static function days_ago($date){
        $date1 = new DateTime($date);
        $date2 = new DateTime(date(DATE_FORMAT));

        $interval = $date1->diff($date2);
        
        if($interval->i <= 0) return $interval->s . " sec";
        else if($interval->h <= 0) return $interval->i . " min";
        else if($interval->d <= 0) return $interval->h . " hour";
        else if($interval->m <= 0) return $interval->d . " day";
        else if($interval->y <= 0) return $interval->m . " month";
        else if($interval->y > 0) return $interval->y . " year";
    }

    public static function format_only_date($date){
        $date1 = new DateTime($date);
        return date_format($date1, 'M j, Y');
    }

    public static function format_time($time){
        $date = date_create($time);
        return date_format($date, 'g:ia, M j, Y');
    }

    public static function format_date($time){
        $date = date_create($time);
        return date_format($date, 'Y-n-j');
    }
    
    public static function today(){
        $date = new DateTime();
        return date_format($date, 'Y-m-d');
    }
    

    public static function format_text($text, $char_count = 20){
        if(strlen($text) >$char_count) $text = substr($text, 0, $char_count) . "...";
        return $text;
    }










    public static function curl_mail_sender($current_page, $param){
        $url  = (isset($_SERVER['HTTPS'])) ? "https://" : "http://";
        $url .= $_SERVER['HTTP_HOST'];
        $url .= str_replace($current_page, "send-mail.php", $_SERVER['REQUEST_URI']);
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Helper::param_str($param));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);
        return $server_output;
    }


    private static function param_str($param){
        $param_str = "";
        foreach ($param as $key => $value){
            $param_str .= $key . "=" .$value . "&";
        }
        return substr(trim($param_str), 0, -1);
    }
    
    
    public static function curl_post($url, $param){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Helper::param_str($param));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);
        return $server_output;
    }

    
    
    public static function curl_get($url){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);
        return $server_output;
    }

    
    public static function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }


    public static function price($price){
        return number_format((float)$price, 2, '.', '');
    }

  

}