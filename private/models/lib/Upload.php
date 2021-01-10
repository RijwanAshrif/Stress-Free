<?php

class Upload{

    private $basename;
    public $size;
    public $file_name;
    private $extension;
    public $type = IMAGE_FILE;
    private $temp_file;
    private $errors;
    private $upload_errors;
    private $max_size;
    public $resolution;
    public $video_size;
    public $upload_folder;
    public $thumb_folder;
    public $thumb = true;


    function __construct($input){
        $this->max_size =  $this->parse_size(ini_get('upload_max_filesize'));
        $this->extension = pathinfo($input['name'], PATHINFO_EXTENSION);
        $this->basename = str_replace(("." . $this->extension), "", $input['name']);
        $this->basename = str_replace("-", " ", $this->basename);
        $this->basename = str_replace(" ", "_", $this->basename);
        $this->size = $input['size'];
        $this->temp_file = $input['tmp_name'];
        $this->upload_errors = $input['error'];
        $this->errors = new Errors();
        $this->file_name = $this->basename . "." . $this->extension;
        $this->audio_duration = "";
        $this->audio_image = "";
        $this->upload_folder = UPLOAD_FOLDER;
        $this->thumb_folder = $this->upload_folder . UPLOADED_THUMB_FOLDER . DIRECTORY_SEPARATOR;
    }


    public function set_folder($upload_folder){
        $this->upload_folder = $upload_folder;
        $this->thumb_folder = $this->upload_folder . UPLOADED_THUMB_FOLDER . DIRECTORY_SEPARATOR;
    }

    function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else return round($size);
    }


    public function get_errors(){
        return $this->errors;
    }

    public function set_type($type){
        $this->type = strtolower($type);
    }

    public function set_resolution(){
        $res_arr = getimagesize($this->upload_folder . $this->get_file_name());
        $this->resolution = $res_arr[0] . ":" . $res_arr[1];
    }

    public function get_resolution(){
        $resolution = "";
        if($this->type == IMAGE_FILE){
            $res_arr = getimagesize($this->upload_folder . $this->get_file_name());
            $resolution = $res_arr[0] . ":" . $res_arr[1];
        }else if($this->type == VIDEO_FILE){
            
        }else $resolution = "Unknown";
        return $resolution;
    }
    

    public function set_max_size($max_size){
        $this->max_size = $max_size;
    }

    private function validate_size(){
        if ($this->size > ($this->max_size * 1024 * 1024)) {
            return false;
        }
        return true;
    }

    public function get_file_ext(){
        return $this->extension;
    }
    
    public function get_file_name(){
        return $this->basename . "." . $this->extension;
    }

    private function validate_extension(){
        $img_ext = strtolower($this->extension);

        if($this->type == IMAGE_FILE){
            if($img_ext != "jpg" && $img_ext != "png" && $img_ext != "jpeg" && $img_ext != "gif") {
                return false;
            }else return true;
        }else if($this->type == VIDEO_FILE){
            if($img_ext != "mp4") return false;
            else return true;

        }else if($this->type == AUDIO_FILE){

            $valid_file_type = false;
            foreach (SUPPORTED_AUDIO as $audio_type){
                if($img_ext == $audio_type) $valid_file_type = true;
            }
            return $valid_file_type;
        }
    }


    private function validate_extension_header(){
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $this->temp_file);

        if($this->type == IMAGE_FILE){
            if($mime == "image/png" || $mime == "image/jpg" || $mime == "image/jpeg") return true;
            else return false;
        }elseif ($this->type == VIDEO_FILE){
            if($mime == "video/mp4") return true;
            else return false;
        }else if ($this->type == AUDIO_FILE){
            
            $valid_mine_type = false;
            foreach (SUPPORTED_AUDIO_MIME as $mime_type){
                if($mime == $mime_type) $valid_mine_type = true;
            }

            return $valid_mine_type;
        }
    }


    private $audio_duration;
    private $audio_image;

    public function get_audio_image_resolution(){
        $file = $this->thumb_folder . $this->audio_image;

        if (!empty($this->audio_image) && file_exists($file)){
            $file_arr = getimagesize($file);
            $resolution = $file_arr[0] . ":" . $file_arr[1];
            return $resolution;
        }else return null;
    }

    public function get_audio_image(){
        return $this->audio_image;
    }

    public function get_duration(){
        return $this->audio_duration;
    }

    public function extract_audio_data(){

        $file = $this->upload_folder . UPLOADED_AUDIO_FOLDER . '/' . $this->get_file_name();
        
        if (file_exists($file)){

            $getID3 = new getID3;
            $fileInfo = $getID3->analyze($file);

            $this->audio_duration = round($fileInfo['playtime_seconds']);
            

            if(key_exists("id3v2", $fileInfo)){
                $id3v2 = $fileInfo['id3v2'];

                if(key_exists("APIC", $id3v2)){

                    $picture = $id3v2['APIC'][0]['data']; // binary image data
                    $path = $this->upload_folder . $this->basename . '.jpg';

                    
                    imagejpeg(imagecreatefromstring($picture), $path);
                    $this->audio_image = $this->basename . '.jpg';

                    $this->thumbnail(300, 300, $this->audio_image);
                }
            }
        }
    }

    public function upload(){
        if(!is_writable($this->upload_folder) || !is_dir($this->upload_folder) || !is_writable($this->thumb_folder) || !is_dir($this->thumb_folder)){
            $this->errors->add_error("Upload folder must be a valid writable folder");
        }else if(!$this->validate_extension()){

            $this->errors->add_error("Invalid File");
            
        }else if(!$this->validate_size()){
            $this->errors->add_error("File can't be over " . $this->max_size . " MB");
        }

        
        if(empty($this->errors->errors)) {
            if($this->type == AUDIO_FILE){

                if (file_exists($this->upload_folder . UPLOADED_AUDIO_FOLDER . DIRECTORY_SEPARATOR . $this->basename . "." . $this->extension)) {

                    $this->basename = Helper::unique_code(16);
                    $this->file_name = $this->basename . "." . $this->extension;
                }

            }else if($this->type == IMAGE_FILE){

                if (file_exists($this->upload_folder . $this->basename . "." . $this->extension)) {

                    $this->basename = Helper::unique_code(16);
                    $this->file_name = $this->basename . "." . $this->extension;
                }
            }
            
            if($this->type == AUDIO_FILE) $target_file = $this->upload_folder . UPLOADED_AUDIO_FOLDER . DIRECTORY_SEPARATOR . $this->basename . "." . $this->extension;
            else if($this->type == VIDEO_FILE) $target_file = $this->upload_folder . UPLOADED_VIDEO_FOLDER . DIRECTORY_SEPARATOR . $this->basename . "." . $this->extension;
            else if($this->type == IMAGE_FILE) $target_file = $this->upload_folder . $this->basename . "." . $this->extension;;


            if(move_uploaded_file($this->temp_file, $target_file)){

                if($this->type == AUDIO_FILE){

                    $this->extract_audio_data();
                    return true;

                } else if($this->type == IMAGE_FILE) {

                    $res_arr = getimagesize($target_file);
                    $this->resolution = $res_arr[0] . ":" . $res_arr[1];

                    if($this->thumb){
                        if($this->thumbnail(300, 300, $this->file_name)) return true;
                        else $this->errors->add_error("Something Went Wrong Generating Thumb Image.");
                    }

                    return true;
                }

            }else{
                $this->error_message();
                return false;
            }
        }
    }


    private function thumbnail($maxw, $maxh, $image_name) {
        $jpg = $this->upload_folder . $image_name;

        $result = 0;
        if($jpg){
            list($width, $height) = getimagesize($jpg); //$type will return the type of the image

            $img_ext = strtolower($this->extension);
            if(($img_ext == "jpg") || ($img_ext == "jpeg")) $source = imagecreatefromjpeg( $jpg );
            else if($img_ext == "png") $source = imagecreatefrompng( $jpg );
            else if($img_ext == "gif") $source = imagecreatefromgif( $jpg );
            else $source = imagecreatefromjpeg( $jpg );

            if (!$source) {
                $source = imagecreatefromstring(file_get_contents($jpg));
            }


            if( $maxw >= $width && $maxh >= $height )  $ratio = 1;
            elseif( $width > $height ) $ratio = $maxw / $width;
            else $ratio = $maxh / $height;

            $thumb_width = round( $width * $ratio ); //get the smaller value from cal # floor()
            $thumb_height = round( $height * $ratio );

            $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

            if(($img_ext == "png") ||($img_ext == "gif")){
                imagealphablending($thumb, false);
                imagesavealpha($thumb,true);
            }

            imagecopyresampled( $thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height );
            $path = $this->thumb_folder . $image_name;
            
            if(($img_ext == "jpg") || ($img_ext == "jpeg")) $result = imagejpeg( $thumb, $path, 90);
            else if($img_ext == "png") $result = imagepng( $thumb, $path, 9);
            else if($img_ext == "gif") $result = imagegif( $thumb, $path, 9);
            else $result = imagejpeg( $thumb, $path, 90);
            

        }
        imagedestroy($thumb);
        imagedestroy($source);
        
        return $result;
    }


    private function error_message(){
        if($this->upload_errors == 1)
            $this->errors->add_error("The uploaded file exceeds the upload_max_filesize directive in php.ini.");
        else if($this->upload_errors == 2)
            $this->errors->add_error("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.");
        else if($this->upload_errors == 3)
            $this->errors->add_error("The uploaded file was only partially uploaded.");
        else if($this->upload_errors == 4)
            $this->errors->add_error("No file was uploaded.");
        else if($this->upload_errors == 6)
            $this->errors->add_error("Missing a temporary folder.");
        else if($this->upload_errors == 7)
            $this->errors->add_error("Failed to write file to disk.");
        else if($this->upload_errors == 8)
            $this->errors->add_error("A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension 
            caused the file upload to stop; 
            examining the list of loaded extensions with phpinfo() may help.");
        else
            $this->errors->add_error("Something went wrong while uploading. Please try again");

    }

    public static function delete($folder, $file_name, $type = IMAGE_FILE){
        $success = false;

        $target_file = $folder . $file_name;
        if (file_exists($target_file) && !empty(trim($target_file))) {
            unlink($target_file);
            $success = true;
        }

        $thumb_file = $folder . UPLOADED_THUMB_FOLDER . DIRECTORY_SEPARATOR . $file_name;



        if ($type = IMAGE_FILE && file_exists($thumb_file)) {
            unlink($thumb_file);
            $success = true;
        }
        return $success;
    }
    

}