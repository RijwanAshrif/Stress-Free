<?php

ob_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Dhaka');

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

define("DB_SERVER", "localhost"); // Server Name
define("DB_USER", "root"); // Database User
define("DB_PASS", ""); // Database Password
define("DB_NAME", "likhon"); // Database Name

define("SERVER_ROOT", $protocol . "localhost/likho/");

define("PUBLIC_FOLDER", "public");
define("SERVER_URL", SERVER_ROOT . PUBLIC_FOLDER . '/');


define("GOOGLE_LOGIN_CLIENT_ID", "304369162519-amvapfikdv3a0nd7a3neeqfl71j1qg6v.apps.googleusercontent.com");
define("GOOGLE_LOGIN_CLIENT_SECRET", "skmaAIkQ-vdLYdCXl2ghP0oh");
define("GOOGLE_LOGIN_REDIRECT_URL", SERVER_URL . "index.php");


define("FACEBOOK_LOGIN_APP_ID", "573243070127273");
define("FACEBOOK_LOGIN_APP_SECRET", "433bbccd57a9dd9a804961ba545ebfb6");
define("FACEBOOK_LOGIN_REDIRECT_URL", SERVER_URL . "index.php");


define("UPLOADED_FOLDER", "uploads");    // Image/Video Upload Folder
define("UPLOADED_THUMB_FOLDER", "thumb");   // Thumb Image Upload Folder
define("UPLOADED_AUDIO_FOLDER", "audio");   // Thumb Image Upload Folder
define("UPLOADED_VIDEO_FOLDER", "video");   // Thumb Image Upload Folder
define("ADMIN_DIR", "admin");
define("ADMIN_URL", SERVER_URL . ADMIN_DIR . "/");

define("TRACK_TYPE_AUDIO", 1);
define("TRACK_TYPE_YOUTUBE", 2);

define("GENDER_TYPE_MALE", 1);
define("GENDER_TYPE_FEMALE", 2);

define("USER_TYPE_EMAIL", 1);
define("USER_TYPE_FACEBOOK", 2);
define("USER_TYPE_GMAIL", 3);

define("API_PAGINATION", 12);    //Pagination item count in the api
define("BACKEND_PAGINATION", 10); // Pagination item count in the admin panel
define("MAX_IMAGE_SIZE", 1.5);    // Maximum Image Size 1.5 mb(Max Value of server 1.5mb(To change open .htaccess file))
define("MAX_AUDIO_SIZE", 10);    // Maximum Video Size 40 mb(Max Value of server 15mb(To change open .htaccess file))
define("MAX_VIDEO_SIZE", 25);    // Maximum Video Size 40 mb(Max Value of server 15mb(To change open .htaccess file))
define("MAX_FILE_COUNT", 10);   // Maximum File Count in One Upload(Max Value of server 10(To change open .htaccess file))
define("DATE_FORMAT", "Y-m-d H:i:s");
define("TRACK_HISTORY", 20);
define("SEARCH_TERM_COUNT", 15);

define("DOWNLOAD_LINK_ACTIVE_TIMING", 10);

define("AUDIO_FILE", "audio");
define("VIDEO_FILE", "video");
define("IMAGE_FILE", "img");

define("IMAGE_RATIO", "1/1");


define("SUPPORTED_AUDIO", ["mp3", "wav", "m4a"]);
define("SUPPORTED_AUDIO_MIME", ["audio/mpeg", "audio/x-wav", "audio/x-m4a", "application/octet-stream", "video/mp4"]);


define("PRIVATE_PATH", dirname(__FILE__));
define("PROJECT_PATH", dirname(PRIVATE_PATH));
define("PUBLIC_PATH", PROJECT_PATH . DIRECTORY_SEPARATOR . PUBLIC_FOLDER);
define("UPLOAD_FOLDER", PUBLIC_PATH . DIRECTORY_SEPARATOR . UPLOADED_FOLDER . DIRECTORY_SEPARATOR);
define("ADMIN_UPLOAD_FOLDER", PUBLIC_PATH . DIRECTORY_SEPARATOR . ADMIN_DIR . DIRECTORY_SEPARATOR . UPLOADED_FOLDER . DIRECTORY_SEPARATOR);

define("IMAGE_LINK", SERVER_URL . UPLOADED_FOLDER . '/');
define("THUMB_LINK", IMAGE_LINK . UPLOADED_THUMB_FOLDER . '/');

define("ADMIN_AUDIO_LINK", ADMIN_URL . UPLOADED_FOLDER . '/' . UPLOADED_AUDIO_FOLDER . '/');
define("ADMIN_IMAGE_LINK", ADMIN_URL . UPLOADED_FOLDER . '/');
define("ADMIN_THUMB_LINK", ADMIN_IMAGE_LINK . UPLOADED_THUMB_FOLDER . '/');

define("API_USERNAME", "api_user_12s");
define("API_USER_SECRET", "123ewesdrgs");

define("ADMIN_TOKEN", "d3gerg$4rsd");

define("ENCRYPTION_KEY", "fedcba9876543210");
define("ENCRYPTION_IV", "0123456789abcdef");


define("ADD_SEARCH_TERM_API", SERVER_URL . "api/search/add.php");


require_once('vendor/getid3/getid3.php');

define("STATUS_ACTIVE", 1);
define("STATUS_DEACTIVE", 2);

define("MIN_PASSWORD_LENGTH", 6);


define("MY_MUSIC_ACTION", "my-music");


define("MAIN_ACTION", "main-action");
define("PROFILE_ACTION", "profile-action");
define("PROFILE_INFO_ACTION", "profile-info-action");
define("UPDATE_PASSWORD_ACTION", "update-password-action");
define("USER_IMAGE_ACTION", "user-image-action");

define("DOWNLOAD_ACTION", "download-action");



define("USER_INFO", "user-info");
define("USER_PASSWORD", "user-password");


define("DEFAULT_PLAYLIST_IMAGE", "images/disc.jpg");
define("BEFORE_LOAD_IMAGE", "images/default.jpg");

define("SONG_DOWNLOAD_ACTION", "song-download-action");
define("SAVE_PLAYLIST_ACTION", "save-playlist-action");
define("UN_SAVE_PLAYLIST_ACTION", "un-save-playlist-action");

define("ADD_TO_FAVOURITE_ACTION", "add-to-favourite-action");
define("REMOVE_FAVOURITE_ACTION", "remove-favourite-action");
define("SONG_DETAIL_ACTION", "song-detail-action");
define("REGISTER_ACTION", "register-action");
define("LOGIN_ACTION", "login-action");
define("FORGOT_PASSWORD_ACTION", "forgot-password");
define("ARTIST_TRACKS_ACTION", "artist-tracks-action");
define("PLAYLIST_BY_USER_ACTION", "playlist-by-user-action");
define("CREATE_PLAYLIST_ACTION", "create-playlist-action");
define("DELETE_PLAYLIST_ACTION", "delete-playlist-action");
define("ADD_TO_PLAYLIST_ACTION", "add-to-playlist-action");
define("REMOVE_FROM_PLAYLIST_ACTION", "remove-from-playlist-action");
define("INCREASE_LISTENING_COUNT_ACTION", "increase-listening-count-action");

define("SEARCHED_TERMS_ACTION", "searched-term-action");
define("SEARCH_ACTION", "search-action");
define("ARTISTS_ACTION", "artists-action");
define("ALBUMS_ACTION", "albums-action");
define("PLAYLISTS_ACTION", "playlists-action");
define("TAGS_ACTION", "tags-action");
define("GENRES_ACTION", "genres-action");
define("TRACK_DETAIL_ACTION", "track-detail-action");

define("MAIN_API", SERVER_URL . "api/curl/functions.php");




define("PLAYLIST_GENERAL", "artist-general");
define("PLAYLIST_TRACKS", "artist-tracks");

define("PLAYLIST_GENERAL_API", ADMIN_URL . "admin-api/playlist/general.php");
define("PLAYLIST_ALL_API", ADMIN_URL . "admin-api/playlist/all.php");
define("PLAYLIST_DELETE_API", ADMIN_URL . "admin-api/playlist/delete.php");
define("PLAYLIST_IMAGE_API", ADMIN_URL . "admin-api/playlist/image.php");
define("PLAYLIST_TRACKS_API", ADMIN_URL . "admin-api/playlist/tracks.php");
define("PLAYLIST_TRACKS_ALL_API", ADMIN_URL . "admin-api/playlist/all-tracks.php");



define("ADMIN_AS_USER_ID", -999);
define("ARTIST_GENERAL", "artist-general");
define("ARTIST_TRACKS", "artist-tracks");

define("ARTIST_GENERAL_API", ADMIN_URL . "admin-api/artist/general.php");
define("ARTIST_ALL_API", ADMIN_URL . "admin-api/artist/all.php");
define("ARTIST_IMAGE_API", ADMIN_URL . "admin-api/artist/image.php");
define("ARTIST_DELETE_API", ADMIN_URL . "admin-api/artist/delete.php");
define("ARTIST_NAMES_API", ADMIN_URL . "admin-api/artist/names.php");
define("ARTIST_TRACKS_ALL_API", ADMIN_URL . "admin-api/artist/all-tracks.php");
define("ARTIST_TRACKS_API", ADMIN_URL . "admin-api/artist/tracks.php");

define("GENRE_GENERAL", "genre-general");

define("GENRE_ALL_API", "admin-api/genre/all.php");
define("GENRE_GENERAL_API", "admin-api/genre/general.php");
define("GENRE_TRACKS_ALL_API", "admin-api/genre/all-tracks.php");


define("GENRE_ADD_API", ADMIN_URL . "admin-api/genre/add.php");
define("GENRE_DELETE_API", ADMIN_URL . "admin-api/genre/delete.php");
define("GENRE_NAMES_API", ADMIN_URL . "admin-api/genre/names.php");

define("TAG_ADD_API", ADMIN_URL . "admin-api/tag/add.php");
define("TAG_DELETE_API", ADMIN_URL . "admin-api/tag/delete.php");
define("TAG_NAMES_API", ADMIN_URL . "admin-api/tag/names.php");


define("ALBUM_GENERAL", "album-general");
define("ALBUM_DESCRIPTION", "album-description");
define("ALBUM_TRACKS", "album-upload");

define("ALBUM_GENERAL_API", ADMIN_URL . "admin-api/album/general.php");
define("ALBUM_DESCRIPTION_API", ADMIN_URL . "admin-api/album/description.php");
define("ALBUM_ALL_API", ADMIN_URL . "admin-api/album/all.php");
define("ALBUM_IMAGE_API", ADMIN_URL . "admin-api/album/image.php");
define("ALBUM_DELETE_API", ADMIN_URL . "admin-api/album/delete.php");
define("ALBUM_TRACKS_API", ADMIN_URL . "admin-api/album/tracks.php");
define("ALBUM_TRACKS_ALL_API", ADMIN_URL . "admin-api/album/all-tracks.php");
define("MULTIPLE_TRACK_UPLOAD_API", ADMIN_URL . "admin-api/album/upload-tracks.php");

define("ALBUM_DROPDOWN_API", ADMIN_URL . "admin-api/album/dropdown.php");



define("TRACK_GENERAL", "track-general");
define("TRACK_DESCRIPTION", "track-description");
define("TRACK_LYRICS", "track-lyrics");

define("TRACK_GENERAL_API", ADMIN_URL . "admin-api/track/general.php");
define("TRACK_DESCRIPTION_API", ADMIN_URL . "admin-api/track/description.php");
define("TRACK_LYRICS_API", ADMIN_URL . "admin-api/track/lyrics.php");
define("TRACK_ALL_API", ADMIN_URL . "admin-api/track/all.php");
define("TRACK_DELETE_API", ADMIN_URL . "admin-api/track/delete.php");
define("TRACK_UPLOAD_API", ADMIN_URL . "admin-api/track/upload-track.php");
define("TRACK_LINK_SAVE_URL", ADMIN_URL . "admin-api/track/add-audio-link.php");
define("TRACK_IMAGE_API", ADMIN_URL . "admin-api/track/image.php");
define("TRACK_SEARCH_API", ADMIN_URL . "admin-api/track/search.php");
define("TRACK_ADD_TO_PLAYLIST_API", ADMIN_URL . "admin-api/track/add-to-playlist.php");
define("TRACK_REMOVE_FROM_PLAYLIST_API", ADMIN_URL . "admin-api/track/remove-from-playlist.php");




define("USER_ALL_API", ADMIN_URL . "admin-api/user/all.php");
define("USER_DELETE_API", ADMIN_URL . "admin-api/user/delete.php");


define("APP_FEEDBACK_ALL_API", ADMIN_URL . "admin-api/app-feedback/all.php");
define("APP_FEEDBACK_DELETE_API", ADMIN_URL . "admin-api/app-feedback/delete.php");


define("PUSH_GENERAL", "push-general");

define("PUSH_ALL_API", ADMIN_URL . "admin-api/push-notification/all.php");
define("PUSH_DELETE_API", ADMIN_URL . "admin-api/push-notification/delete.php");
define("PUSH_GENERAL_API", ADMIN_URL . "admin-api/push-notification/general.php");
define("PUSH_NOTIFICATION_NOTIFY_API", ADMIN_URL . "admin-api/push-notification/notify.php");

define("CONFIG_SITE", "site-configuration");
define("CONFIG_SMTP", "smtp-configuration");
define("CONFIG_API_TOKEN", "api-token");
define("CONFIG_PUSH_NOTIFICATION", "push-configuration");

define("CONFIG_SITE_API", ADMIN_URL . "admin-api/site-config/configuration.php");
define("CONFIG_SMTP_API", ADMIN_URL . "admin-api/site-config/smtp.php");
define("CONFIG_API_TOKEN_API", ADMIN_URL . "admin-api/site-config/api-token.php");
define("CONFIG_IMAGE_API", ADMIN_URL . "admin-api/site-config/add-site-image.php");
define("CONFIG_PUSH_NOTIFICATION_API", ADMIN_URL . "admin-api/site-config/push-notification.php");

define("ADMOB_BANNER", "banner-admob");
define("ADMOB_INTERSTITIAL", "interstitial-admob");

define("ADMOB_BANNER_API", ADMIN_URL . "admin-api/admob/banner.php");
define("ADMOB_INTERSTITIAL_API", ADMIN_URL . "admin-api/admob/interstitial.php");

define("ADMIN_INFO", "admin-info");
define("ADMIN_CREDENTIAL", "admin-credential");
define("ADMIN_PASSWORD", "admin-password");

define("ADMIN_LOGIN_API", ADMIN_URL . "admin-api/admin/login.php");
define("ADMIN_INFO_API", ADMIN_URL . "admin-api/admin/info.php");
define("ADMIN_CREDENTIAL_API", ADMIN_URL . "admin-api/admin/credential.php");
define("ADMIN_PASSWORD_API", ADMIN_URL . "admin-api/admin/update-password.php");
define("ADMIN_PROFILE_IMAGE", ADMIN_URL . "admin-api/admin/image.php");



define("ACTION_YOUTUBE_DETAILS", "action-youtube-details");
define("GET_YOUTUBE_DETAILS", ADMIN_URL  . "admin-api/curl/functions.php");


define("PROPERTY_GENERAL", "property-general");
define("PROPERTY_ABOUT", "property-about");
define("PROPERTY_SLIDER", "property-slider");
define("PROPERTY_POLICY", "property-policy");
define("PROPERTY_SLIDER_IMAGES", "property-slider-image");

define("PROPERTY_GENERAL_API", ADMIN_URL . "admin-api/property/general.php");
define("PROPERTY_ABOUT_API", ADMIN_URL . "admin-api/property/about.php");
define("PROPERTY_SLIDER_API", ADMIN_URL . "admin-api/property/slider.php");
define("PROPERTY_POLICY_API", ADMIN_URL . "admin-api/property/policy.php");
define("PROPERTY_SLIDER_IMAGES_API", ADMIN_URL . "admin-api/property/slider-images.php");


define("UPLOADED_LINK", SERVER_URL . UPLOADED_FOLDER . "/");

define("DEFAULT_IMAGE", "default_image.png");
define("PROFILE_DEFAULT", "profile_default.jpg");
define("DEFAULT_RESOLUTION", "500:500");



require_once('strings.php');
require_once('models/lib/Database.php');
require_once('models/lib/Helper.php');
require_once('models/lib/API_Helper.php');
require_once('models/lib/Session.php');
require_once('models/lib/Response.php');
require_once('models/lib/Errors.php');
require_once('models/lib/Message.php');
require_once('models/lib/Upload.php');
require_once('models/lib/Mailer.php');
require_once('models/lib/Util.php');
require_once('models/lib/Pagination.php');
require_once('models/lib/Encryption.php');


require_once('models/Admin.php');
require_once('models/Site_Config.php');
require_once('models/Setting.php');
require_once('models/Push_Notification.php');
require_once('models/Admob.php');
require_once('models/User.php');
require_once('models/Smtp_Config.php');

require_once('models/Artist.php');
require_once('models/Album.php');
require_once('models/Track.php');
require_once('models/Genre.php');
require_once('models/Tag.php');
require_once('models/Favourite.php');
require_once('models/User_Token.php');
require_once('models/Playlist.php');
require_once('models/Playlist_Track.php');
require_once('models/Saved_Playlist.php');
require_once('models/Recently_Played.php');
require_once('models/Search_Term.php');
require_once('models/App_Feedback.php');

require_once('vendor/autoload.php');
