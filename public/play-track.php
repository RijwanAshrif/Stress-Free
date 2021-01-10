<?php require_once('../private/init.php');


$encrypted_track = Helper::get_val('track');

if(!isset($_SERVER['HTTP_REFERER'])){
    // redirect them to your desired location
    header('location:../index.php');
    exit;
}else{

    if ($encrypted_track) {
        
        $json_data = json_decode(Encryption::decrypt(ENCRYPTION_KEY, ENCRYPTION_IV, $encrypted_track), true);

        $track = new Track();
        $track = $track->where(["id" => $json_data["id"]])->one();



        if(!empty($track)){

            $file = ADMIN_UPLOAD_FOLDER . UPLOADED_AUDIO_FOLDER . DIRECTORY_SEPARATOR . $track->track_name;

            if (file_exists($file) && is_readable($file))  {

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $file);
                
                header('Content-type: ' . $mime_type);
                header('Content-length: ' . filesize($file));
                header('Content-Disposition: attachment; filename="' . $filename);
                header('X-Pad: avoid browser bug');
                header('Cache-Control: no-cache');
                header('Accept-Ranges: bytes');
                header("Content-Transfer-Encoding: chunked");
                readfile($file);

                return (new Response($file, 200, $headers));

            }else {
                header("HTTP/1.0 404 Not Found");
                echo "<h1>Error 404: File Not Found: <br /><em>$track->track_name</em></h1>";
            }

        }else {
            header("HTTP/1.0 404 Not Found");
            echo "<h1>Error 404: File Not Found</h1>";
        }

    }

}



?>