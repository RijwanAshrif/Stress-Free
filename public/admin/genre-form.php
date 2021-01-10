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
                <li><a href="#<?php echo GENRE_GENERAL; ?>">General</a></li>
            </ul>

            <div class="ajax-form-wrapper loader-wrapper">
                <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>
                
                <form class="ajax-form tab-form artist" id="<?php echo GENRE_GENERAL; ?>" method="post"
                      data-url="<?php echo GENRE_GENERAL_API; ?>">

                    <input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>
                    <a href="#" class="head ajax-sidebar-dropdown">
                        <h5 class="title">Genre</h5>
                        <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                    </a>


                    <div class="item-content">
                        <div class="ajax-bar"></div>

                        <h5 class="mt-10 mb-30 ajax-message"></h5>

                        <label>Name</label>
                        <input type="text" data-ajax-field="true" placeholder="Title" name="title" value=""/>

                        <div class="btn-wrapper">
                            <a href="#" class="float-l oflow-hidden mt-5">
                                
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


<?php echo "<script>defaultImage = '" . DEFAULT_IMAGE . "'</script>"; ?>
<?php echo "<script>uploadedLink = '" . ADMIN_IMAGE_LINK . "'</script>"; ?>
<?php echo "<script>uploadedThumbLink = '" . ADMIN_THUMB_LINK . "'</script>"; ?>
<?php echo "<script>maxUploadedFile = '" . MAX_IMAGE_SIZE  . "'</script>"; ?>

<?php require("common/php/php-footer.php"); ?>

<script>

    /*MAIN SCRIPTS*/
    (function ($) {
        "use strict";

        ajaxSidebarInit(window.location.hash, '<?php echo GENRE_GENERAL; ?>');
        
    })(jQuery);

</script>

