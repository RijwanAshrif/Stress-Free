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
                <li><a href="#<?php echo ALBUM_GENERAL; ?>">General</a></li>
                <li><a href="#<?php echo ALBUM_DESCRIPTION; ?>">Description</a></li>
                <li><a href="#<?php echo ALBUM_TRACKS; ?>">Tracks</a></li>
            </ul>

            <div class="ajax-form-wrapper loader-wrapper">
                <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>

                <form class="ajax-form tab-form album" id="<?php echo ALBUM_TRACKS; ?>" method="post" data-url="<?php echo ALBUM_TRACKS_API; ?>">

                    <input type="hidden" name="album_id" value="<?php echo Helper::get_val("id"); ?>"/>
                    <a href="#" class="head ajax-sidebar-dropdown">
                        <h5 class="title">Album</h5>
                        <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                    </a>


                    <div class="item-content">
                        <div class="ajax-bar"></div>
                        <h5 class="mt-10 mb-30 ajax-message"></h5>

                        <label class="mb-15">Audio Upload( Supported files : Supported files : <?php echo implode('/', SUPPORTED_AUDIO);?> )(You can upload up to <?php echo MAX_FILE_COUNT; ?> files at a time)</label>

                        <div class="mb-20 album-upload"><input type="file" multiple name="tracks" id="multiple-track-upload"
                            data-url="<?php echo MULTIPLE_TRACK_UPLOAD_API?>" data-upload-by="album_id"></div>

                        <h4 class="or-text"><b>OR</b></h4>

                        <label class="">Audio Link( Supported files : <?php echo implode('/', SUPPORTED_AUDIO);?> )</label>

                        <div class="link-input">

                            <div class="youtube-link-wrapper" id="audio-link-add">
                                <input type="text" placeholder="eg. https://domain.com/title.mp3" name="audio_link">
                                <a href="#"
                                   data-url="<?php echo TRACK_LINK_SAVE_URL; ?>"
                                   data-upload-by="album_id"
                                   class="c-btn mb-10"><b>Save</b>
                                </a>

                            </div><!--audio-upload-wrapper-->
                        </div><!-- radio-wrapper -->

                        <div class="mt-50"></div>

                        <div class="multiple_tracks album" data-edit-link="track-form.php?id="
                             data-delete-link="<?php echo TRACK_DELETE_API . '?id='; ?>">
                        </div>

                    </div><!--item-content-->
                </form>

                <form class="ajax-form tab-form album" id="<?php echo ALBUM_DESCRIPTION; ?>" method="post" 
                      data-url="<?php echo ALBUM_DESCRIPTION_API; ?>">
                    
                    <input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>
                    <a href="#" class="head ajax-sidebar-dropdown">
                        <h5 class="title">Album</h5>
                        <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                    </a>

                    <div class="item-content">
                        <div class="ajax-bar"></div>
                        <h5 class="mt-10 mb-30 ajax-message"></h5>

                        <label>Description</label>
                        <textarea data-ajax-field="true" placeholder="Description" name="description" ></textarea>

                        <div class="btn-wrapper">
                           
                            <button type="submit" class="c-btn mb-10"><b>Save</b></button>
                        </div>

                    </div><!--item-content-->
                </form>


                <form class="ajax-form tab-form album" id="<?php echo ALBUM_GENERAL; ?>" method="post"
                      data-url="<?php echo ALBUM_GENERAL_API; ?>">
                    <input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>
                    <a href="#" class="head ajax-sidebar-dropdown">
                        <h5 class="title">Album</h5>
                        <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                    </a>

                    <div class="item-content">
                        <div class="ajax-bar"></div>

                        <h5 class="mt-10 mb-30 ajax-message"></h5>

                        <label class="control-label" for="file">Logo(<?php echo "Max Image Size : " . MAX_IMAGE_SIZE . "MB. Required Format : png/jpg/jpeg"; ?>)</label>

                        <div class="image-upload">
                            <div class="dplay-tbl">
                                <div class="dplay-tbl-cell">
                                    <img data-logo-element=".album" class="max-h-200x uploaded-image album image_name" alt="" src=""/>
                                </div>
                            </div>

                            <input data-url="<?php echo ALBUM_IMAGE_API; ?>" type="file" class="ajax-img-upload" name="image_name" />
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


                        <label>Title</label>
                        <input type="text" data-ajax-field="true" placeholder="Title" name="title" value=""/>




                        <div class="dropdown-search-input" id="artists">
                            <label>Artists</label>
                            <div class="readonly-input" data-ajax-field="readonly-input" data-url="<?php echo ARTIST_NAMES_API; ?>">
                                <input type="hidden" name="artists" value=",">

                                <div class="selected-items">
                                    <a class="down-btn"><i class="ion-chevron-down"></i></a>
                                    <a class="no-selected active">Select Genres</a>
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

                                        <div class="db-items" ></div>
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
                                    <a class="no-selected active">Select Tags</a>
                                </div>
                            </div>

                        </div><!--dropdown-search-input-->


                        <div class="dropdown-search-input" id="tags">
                            <label>Tags</label>

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

            </div><!--room-form-->
        </div><!--main-content-inner-->
    </div><!--main-content-->
</div><!--main-container-->


<?php echo "<script>maxUploadedFileCount = '" . MAX_FILE_COUNT . "'</script>"; ?>
<?php echo "<script>defaultImage = '" . DEFAULT_IMAGE . "'</script>"; ?>
<?php echo "<script>uploadedLink = '" . ADMIN_IMAGE_LINK . "'</script>"; ?>
<?php echo "<script>uploadedAudioLink = '" . ADMIN_AUDIO_LINK . "'</script>"; ?>
<?php echo "<script>uploadedThumbLink = '" . ADMIN_THUMB_LINK . "'</script>"; ?>
<?php echo "<script>maxUploadedFile = '" . MAX_IMAGE_SIZE  . "'</script>"; ?>
<?php echo "<script>maxTrackSize = '" . MAX_AUDIO_SIZE  . "'</script>"; ?>
<?php echo "<script>youtubeType = '" . TRACK_TYPE_YOUTUBE . "'</script>"; ?>
<?php echo "<script>uploadedType = '" . TRACK_TYPE_AUDIO . "'</script>"; ?>
<?php echo "<script>supportedAudio = '" . json_encode(SUPPORTED_AUDIO) . "'</script>"; ?>
<script> supportedAudio = JSON.parse(supportedAudio); </script>

<?php require("common/php/php-footer.php"); ?>

<script>

    /*MAIN SCRIPTS*/
    (function ($) {
        "use strict";

        ajaxSidebarInit(window.location.hash, '<?php echo ALBUM_GENERAL; ?>');

    })(jQuery);

</script>

