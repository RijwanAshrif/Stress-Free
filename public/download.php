<?php require_once('../private/init.php');

$encrypted_track = Helper::get_val('d');

if ($encrypted_track) {
    $json_data = json_decode(Encryption::decrypt(ENCRYPTION_KEY, ENCRYPTION_IV, $encrypted_track), true);

    $track_id = $json_data["id"];
    $time = $json_data["time"];
    $error_message = null;

    if(!empty($track_id) && !empty($time)){

        if(Helper::time_difference($time)->i < DOWNLOAD_LINK_ACTIVE_TIMING){

            $track = new Track();
            $track = $track->where(["id" => $json_data["id"]])->one();

            if(!empty($track)){

                $file = ADMIN_UPLOAD_FOLDER . UPLOADED_AUDIO_FOLDER . DIRECTORY_SEPARATOR . $track->track_name;

                if (file_exists($file) && is_readable($file))  {

                    header('Content-type: application/mp3');
                    header("Content-Disposition: attachment; filename=\"$track->track_name\"");
                    readfile($file);

                }else $error_message = "Error 404: File Not Found: <br /><em>" . $track->track_name . "</em>";
            }else $error_message = "Error 404: File Not Found";
        }else $error_message = "Error 404: Invalid Link";
    }else $error_message = "Error 404: Invalid Link";


    if($error_message != null){
        header("HTTP/1.0 404 Not Found");
        echo "<h2>" . $error_message . "</h2>";
    }
}

?>