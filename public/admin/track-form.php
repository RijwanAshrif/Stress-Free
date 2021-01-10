<?php require_once('../../private/init.php'); ?>

<?php

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
                <li><a href="#<?php echo TRACK_GENERAL; ?>">General</a></li>
                <li><a href="#<?php echo TRACK_DESCRIPTION; ?>">Description</a></li>
            </ul>

            <div class="ajax-form-wrapper loader-wrapper">
                <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>


                <form class="ajax-form tab-form track" id="<?php echo TRACK_DESCRIPTION; ?>"
                      method="post" data-url="<?php echo TRACK_DESCRIPTION_API; ?>">

                    <input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>
                    <a href="#" class="head ajax-sidebar-dropdown">
                        <h5 class="title">Track</h5>
                        <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                    </a>

                    <div class="item-content">
                        <div class="ajax-bar"></div>

                        <h5 class="mt-10 mb-30 ajax-message"></h5>

                        <input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>

                        <label>Description</label>
                        <textarea name="description" data-ajax-field="true" placeholder="Description"></textarea>

                        <div class="btn-wrapper">
                            <a href="#" class="float-l oflow-hidden mt-5"></a>

                            <button type="submit" class="c-btn mb-10"><b>Save</b></button>
                        </div>
                    </div><!--item-content-->
                </form>


                <form class="ajax-form tab-form track" id="<?php echo TRACK_GENERAL; ?>" method="post" 
                      data-url="<?php echo TRACK_GENERAL_API; ?>">

                    <input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>
                    <a href="#" class="head ajax-sidebar-dropdown">
                        <h5 class="title">Track</h5>
                        <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                    </a>

                    <div class="item-content">
                        <div class="ajax-bar"></div>

                        <h5 class="mt-10 mb-30 ajax-message"></h5>

                        <input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>

                        <label class="radio-label">Audio Track( Supported files : <?php echo implode('/', SUPPORTED_AUDIO);?> )(Max. size <?php echo MAX_AUDIO_SIZE; ?>MB)</label>

                        <div class="radio-wrapper">
                            <div class="custom-radio-style">
                                <input type="radio" name="audio_type" value="<?php echo TRACK_TYPE_AUDIO; ?>">
                                <span class="checkmark"></span>
                            </div><!--custom-radio-style-->
                            
                            <div class="radio-content audio-upload-wrapper">
                                <div class="admin-player player-wrapper ">
                                    <audio controls class="track track_name">
                                        <source src="" type="audio/mpeg">
                                    </audio>
                                </div>

                                <div class="audio-upload">
                                    <input type="file" name="track" data-upload-by="id" id="track-upload"
                                           data-url="<?php echo TRACK_UPLOAD_API; ?>"/>

                                </div><!--audio-upload-->
                            </div><!--audio-upload-wrapper-->
                        </div><!-- radio-wrapper -->

                 
                        
                        <div class="mt-50"></div>

                        <div class="track-image-container">

                            <div class="track-image">

                                <label class="control-label" for="file"><?php echo "Max Size : " . MAX_IMAGE_SIZE . "MB.(png/jpg/jpeg)"; ?></label>

                                <div class="image-upload">
                                    <div class="dplay-tbl">
                                        <div class="dplay-tbl-cell">
                                            <img class="max-h-200x uploaded-image track image_name" alt="" src=""/>
                                        </div>
                                    </div>

                                    <input data-url="<?php echo TRACK_IMAGE_API; ?>" type="file" class="ajax-img-upload" name="image_name" />
                                </div><!--image-upload-->

                            </div><!--track-image-->


                            <div class="track-detail">

                                <label>Title</label>
                                <input type="text" data-ajax-field="true" placeholder="Title" name="title" value=""/>

                                <div class="dropdown-search-input" data-single-dropdown="true" id="album">
                                    <label>Album</label>
                                    <div class="readonly-input image-dropdown" data-url="<?php echo ALBUM_DROPDOWN_API; ?>">
                                        <input type="hidden" name="album">

                                        <div class="selected-item-wrap">
                                            <a class="down-btn"><i class="ion-chevron-down"></i></a>
                                            <div class="selected-item">
                                                <a href="#" class="no-selected active">Select Album</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="search-dropdown to-top bottom-60x" >
                                        <div class="search-dropdown-inner loader-wrapper">

                                            <div class="title mb-10">
                                                <h5>Album</h5>
                                                <a href="#" class="close-search-dropdown"><i class="ion-android-close"></i></a>
                                            </div>

                                            <div class="plr-25 ptb-15">
                                                <div class="ajax-message mb-15"></div>
                                                <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>


                                                <div class="attached-button">
                                                    <input type="text" placeholder="Search Album">
                                                </div><!--attached-button-->

                                                <div class="dropdown-items db-items"></div>
                                            </div>


                                        </div><!--search-dropdown-->
                                    </div><!--search-dropdown-->
                                </div><!--dropdown-search-input-->

                            </div><!--track-detail-->
                            
                        </div><!--track-image-container-->
                        
                        <div class="dropdown-search-input" id="artists">
                            <label>Artists</label>
                            <div class="readonly-input" data-ajax-field="readonly-input" data-url="<?php echo ARTIST_NAMES_API; ?>">
                                <input type="hidden" name="artists" value=",">
                                <div class="selected-items">
                                    <a class="down-btn"><i class="ion-chevron-down"></i></a>
                                    <a class="no-selected active">Select Artists</a>

                                </div>
                            </div>

                            <div class="search-dropdown" >
                                <div class="search-dropdown-inner loader-wrapper">

                                    <div class="title mb-10">
                                        <h5>Artists</h5>
                                        <a href="#" class="close-search-dropdown"><i class="ion-android-close"></i></a>
                                    </div>

                                    <div class="p-25">
                                        <div class="ajax-message mb-15"></div>
                                        <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>


                                        <div class="attached-button">
                                            <input type="text" placeholder="Search Artist">
                                        </div><!--attached-button-->

                                        <div class="db-items"></div>
                                    </div>


                                </div><!--search-dropdown-->
                            </div><!--search-dropdown-->
                        </div><!--dropdown-search-input-->


                        <div class="dropdown-search-input" id="genres">

                            <label>Genres</label>

                            <div class="search-dropdown to-top" >
                                <div class="search-dropdown-inner loader-wrapper">

                                    <div class="title mb-10">
                                        <h5>Genres</h5>
                                        <a href="#" class="close-search-dropdown"><i class="ion-android-close"></i></a>
                                    </div>

                                    <div class="p-25">

                                        <div class="ajax-message mb-15"></div>
                                        <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>

                                        <div class="attached-button add-btn" >
                                            <input type="text" placeholder="Search/Add Genre">
                                            <button data-add-url="<?php echo GENRE_ADD_API; ?>"
                                                    class="active btn-loader loader-sm"><b class="active btn-text">Add</b>
                                                <span class="ajax-loader"><span></span></span></button>
                                        </div><!--attached-button-->

                                        <div class="db-items" data-url="<?php echo GENRE_DELETE_API; ?>"></div>
                                    </div>


                                </div><!--search-dropdown-->
                            </div><!--search-dropdown-->

                            <div class="readonly-input" data-ajax-field="readonly-input" data-url="<?php echo GENRE_NAMES_API; ?>">
                                <input type="hidden" name="genres" value=",">
                                <div class="selected-items">
                                    <a class="down-btn"><i class="ion-chevron-down"></i></a>
                                    <a class="no-selected active">Select Genres</a>
                                </div>
                            </div>

                        </div><!--dropdown-search-input-->


                        <div class="dropdown-search-input" id="tags">
                            <label>Tags(Optional)</label>

                            <div class="search-dropdown to-top" >
                                <div class="search-dropdown-inner loader-wrapper">

                                    <div class="title mb-10">
                                        <h5>Tags</h5>
                                        <a href="#" class="close-search-dropdown"><i class="ion-android-close"></i></a>
                                    </div>

                                    <div class="p-25">
                                        <div class="ajax-message mb-15"></div>
                                        <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>

                                        <div class="attached-button add-btn" >
                                            <input type="text" placeholder="Search/Add Tag">
                                            <button data-add-url="<?php echo TAG_ADD_API; ?>"
                                                    class="active btn-loader loader-sm"><b class="active btn-text">Add</b>
                                                <span class="ajax-loader"><span></span></span></button>
                                        </div><!--attached-button-->

                                        <div class="db-items" data-url="<?php echo TAG_DELETE_API; ?>"></div>
                                    </div>

                                </div><!--search-dropdown-->
                            </div><!--search-dropdown-->

                            <div class="readonly-input" data-url="<?php echo TAG_NAMES_API; ?>">
                                <input type="hidden" placeholder="Tags" name="tags" value=",">
                                
                                <div class="selected-items">
                                    <a class="down-btn"><i class="ion-chevron-down"></i></a>
                                    <a class="no-selected active">Select Tags</a>
                                </div>
                            </div>
                        </div><!--dropdown-search-input-->

                        <div class="btn-wrapper">
                            <a href="#" class="float-l oflow-hidden mt-5">
                                <label class="status switch">
                                    <input type="checkbox" name="status" />
                                     <span class="slider round">
                                        <b class="active">Active</b>
                                        <b class="inactive">Inactive</b>
                                    </span>
                                </label>
                                <span class="toggle-title"></span>
                            </a>

                            <button type="submit" class="c-btn mb-10"><b>Save</b></button>
                        </div>

                    </div><!--item-content-->
                </form>

            </div><!--ajax-form-wrapper loader-wrapper-->

        </div><!--main-content-inner-->
    </div><!--main-content-->
</div><!--main-container-->


<?php echo "<script>maxUploadedFile = '" . MAX_IMAGE_SIZE  . "'</script>"; ?>
<?php echo "<script>maxUploadedFileCount = '" . MAX_FILE_COUNT  . "'</script>"; ?>
<?php echo "<script>defaultImage = '" . DEFAULT_IMAGE . "'</script>"; ?>
<?php echo "<script>uploadedLink = '" . ADMIN_IMAGE_LINK . "'</script>"; ?>
<?php echo "<script>uploadedAudioLink = '" . ADMIN_AUDIO_LINK . "'</script>"; ?>
<?php echo "<script>uploadedThumbLink = '" . ADMIN_THUMB_LINK . "'</script>"; ?>
<?php echo "<script>maxTrackSize = '" . MAX_AUDIO_SIZE  . "'</script>"; ?>
<?php echo "<script>supportedAudio = '" . json_encode(SUPPORTED_AUDIO) . "'</script>"; ?>
<script> supportedAudio = JSON.parse(supportedAudio); </script>


<?php require("common/php/php-footer.php"); ?>

<script>

    /*MAIN SCRIPTS*/
    (function ($) {
        "use strict";

        ajaxSidebarInit(window.location.hash, '<?php echo TRACK_GENERAL; ?>');

    })(jQuery);

</script>

