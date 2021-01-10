<?php require_once('../../private/init.php'); ?>

<?php

$errors = Session::get_temp_session(new Errors());
$message = Session::get_temp_session(new Message());
$admin = Session::get_session(new Admin());

if(empty($admin)) Helper::redirect_to("login.php");

?>

<?php require("common/php/php-head.php"); ?>

<body>

<?php require("common/php/header.php"); ?>

<div class="main-container">

    <?php require("common/php/sidebar.php"); ?>

    <div class="main-content">

        <div class="main-content-inner">

            <ul class="ajax-sidebar">
                <li class="head">Configuration</li>
                <li><a href="#<?php echo PLAYLIST_GENERAL; ?>">General</a></li>
                <li><a href="#<?php echo PLAYLIST_TRACKS; ?>">Tracks</a></li>
            </ul>

            <div class="ajax-form-wrapper loader-wrapper">
                <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>


                <form class="ajax-form tab-form playlist" id="<?php echo PLAYLIST_TRACKS; ?>" method="post"
                        data-url="<?php echo PLAYLIST_TRACKS_API; ?>"
                        data-remove-from-pl-link="<?php echo TRACK_REMOVE_FROM_PLAYLIST_API; ?>">

                    <input type="hidden" name="playlist_id" value="<?php echo Helper::get_val("id"); ?>"/>
                    <a href="#" class="head ajax-sidebar-dropdown">
                        <h5 class="title">Playlist</h5>
                        <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                    </a>


                    <div class="item-content">
                        <div class="ajax-bar"></div>
                        <h5 class="mt-10 mb-30 ajax-message"></h5>


                        <label class="">Search Track</label>

                        <div class="search-track-wrapper">
                            <input type="text" placeholder="eg. Imagine" name="audio_link" id="audio-search"
                                   data-url="<?php echo TRACK_SEARCH_API; ?>">
                            <div id="search-tracks-wrapper">
                                <div class="ajax-bar"></div>
                                <h5 class="mt-10 mb-30 ajax-message"></h5>
                                <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>
                                
                                <div id="searched-tracks"></div>
                            </div><!--search-tracks-wrapper-->

                        </div><!--search-track-wrapper-->
                        
                       

                        <div class="mt-50"></div>

                        <div class="multiple_tracks playlist" data-edit-link="track-form.php?id="
                             data-delete-link="<?php echo TRACK_DELETE_API . '?id='; ?>">
                        </div>

                    </div><!--item-content-->
                </form>


                <form class="ajax-form tab-form artist" id="<?php echo PLAYLIST_GENERAL; ?>" method="post"
                      data-url="<?php echo PLAYLIST_GENERAL_API; ?>">

                    <input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>
                    <a href="#" class="head ajax-sidebar-dropdown">
                        <h5 class="title">Artist</h5>
                        <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                    </a>


                    <div class="item-content">
                        <div class="ajax-bar"></div>

                        <h5 class="mt-10 mb-30 ajax-message"></h5>

                        <label class="control-label" for="file">Logo(<?php echo "Max Image Size : " . MAX_IMAGE_SIZE . "MB. Required Format : png/jpg/jpeg"; ?>)</label>

                        <div class="image-upload">
                            <div class="dplay-tbl">
                                <div class="dplay-tbl-cell">
                                    <img data-logo-element=".artist" class="max-h-200x uploaded-image playlist image_name" alt="" src=""/>
                                </div>
                            </div>

                            <input data-url="<?php echo PLAYLIST_IMAGE_API; ?>" type="file" class="ajax-img-upload" name="image_name" />
                        </div><!--image-upload-->


                        <div class="mb-15">
                            <a href="#" class="oflow-hidden mt-5">
                                <label class="status on-off switch">
                                    <input type="checkbox" name="featured" />
                                     <span class="slider round">
                                        <b class="active">ON</b>
                                        <b class="inactive">OFF</b>
                                    </span>
                                </label>
                                <span class="toggle-title">Featured</span>
                            </a>
                        </div>


                        <label>Name</label>
                        <input type="text" data-ajax-field="true" placeholder="Title" name="title" value=""/>


                        <div class="btn-wrapper">
                            <a href="#" class="float-l oflow-hidden mt-5">
                                <label class="status switch">
                                    <input type="checkbox" name="status" />
                                     <span class="slider round">
                                        <b class="active">Public</b>
                                        <b class="inactive">Private</b>
                                    </span>
                                </label>
                                <span class="toggle-title"></span>
                            </a>

                            <button type="submit" class="c-btn mb-10"><b>Save</b></button>
                        </div>

                    </div><!--item-content-->
                </form>

            </div><!--room-form-->
        </div><!--main-content-inner-->
    </div><!--main-content-->
</div><!--main-container-->


<?php echo "<script>playlistID = '" . Helper::get_val("id"). "'</script>"; ?>
<?php echo "<script>addToPlaylistAPI = '" . TRACK_ADD_TO_PLAYLIST_API . "'</script>"; ?>
<?php echo "<script>maxTrackSize = '" . MAX_AUDIO_SIZE  . "'</script>"; ?>
<?php echo "<script>maxUploadedFileCount = '" . MAX_FILE_COUNT . "'</script>"; ?>
<?php echo "<script>defaultImage = '" . DEFAULT_IMAGE . "'</script>"; ?>
<?php echo "<script>uploadedLink = '" . ADMIN_IMAGE_LINK . "'</script>"; ?>
<?php echo "<script>uploadedAudioLink = '" . ADMIN_AUDIO_LINK . "'</script>"; ?>
<?php echo "<script>uploadedThumbLink = '" . ADMIN_THUMB_LINK . "'</script>"; ?>
<?php echo "<script>maxUploadedFile = '" . MAX_IMAGE_SIZE  . "'</script>"; ?>
<?php echo "<script>youtubeType = '" . TRACK_TYPE_YOUTUBE . "'</script>"; ?>
<?php echo "<script>uploadedType = '" . TRACK_TYPE_AUDIO . "'</script>"; ?>
<?php echo "<script>supportedAudio = '" . json_encode(SUPPORTED_AUDIO) . "'</script>"; ?>
<script> supportedAudio = JSON.parse(supportedAudio); </script>



<?php require("common/php/php-footer.php"); ?>

<script>

    /*MAIN SCRIPTS*/
    (function ($) {
        "use strict";

        ajaxSidebarInit(window.location.hash, '<?php echo PLAYLIST_GENERAL; ?>');
        
    })(jQuery);

</script>

