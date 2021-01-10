<?php require_once('../private/init.php');

$user = Session::get_session(new User());
$logged_in = (!empty($user)) ? true : false;


if(!$logged_in) Helper::redirect_to("index.php");

require("common/php/php-head.php");

?>




<main>

    <div class="container">
        <div class="main-content-inner pos-relative mtb-50">

            <ul class="ajax-sidebar">
                <li class="head">Profile</li>
                <li><a href="#<?php echo USER_INFO; ?>" class="active">General Info</a></li>
                <?php if($user->type == USER_TYPE_EMAIL){ ?>
                    <li><a href="#<?php echo USER_PASSWORD; ?>">Update Password</a></li>
                <?php } ?>
            </ul>

            <div class="ajax-form-wrapper loader-wrapper">
                <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>

                <form class="ajax-form tab-form active" id="<?php echo USER_INFO; ?>" method="post">

                    <a href="#" class="head ajax-sidebar-dropdown">
                        <h5 class="title">User Details</h5>
                        <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                    </a>

                    <div class="item-content">
                        <div class="ajax-bar"></div>

                        <h5 class="mt-10 mb-30 ajax-message"></h5>

                        <input type="hidden" name="action" value="<?php echo PROFILE_INFO_ACTION; ?>"/>
                        <input type="hidden" name="id" value="<?php echo $user->id; ?>"/>

                        <label class="control-label" for="file">Logo(<?php echo "Max Image Size : " . MAX_IMAGE_SIZE . "MB. Required Format : png/jpg/jpeg"; ?>)</label>

                        <div class="image-upload">
                            <div class="dplay-tbl">
                                <div class="dplay-tbl-cell">
                                    <img class="max-h-200x uploaded-image user image_name" alt="" src="<?php echo $profile_url; ?>"/>
                                </div>
                            </div>

                            <input data-action="<?php echo USER_IMAGE_ACTION; ?>" type="file" class="ajax-img-upload" name="image_name" />
                        </div><!--image-upload-->

                        <label>Email</label>
                        <input type="text" placeholder="Email" name="email" value="" readonly/>

                        <label>Username</label>
                        <input type="text" data-ajax-field="true" placeholder="Username" name="username" value=""/>

                        <div class="mt-20" data-ajax-field="radio">

                            <label class="radio-container mr-15">Male
                                <input type="radio" name="gender" value="<?php echo GENDER_TYPE_MALE; ?>">
                                <span class="checkmark"></span>
                            </label>

                            <label class="radio-container">Female
                                <input type="radio" name="gender" value="<?php echo GENDER_TYPE_FEMALE; ?>">
                                <span class="checkmark"></span>
                            </label>

                        </div>

                        <div class="btn-wrapper"><button type="submit" class="c-btn mb-10"><b>Update</b></button></div>
                    </div><!--item-content-->
                </form>


                <?php if($user->type == USER_TYPE_EMAIL){ ?>
                    <form class="ajax-form tab-form" id="<?php echo USER_PASSWORD; ?>" method="post">
                        <a href="#" class="head ajax-sidebar-dropdown">
                            <h5 class="title">Admin Credentials</h5>
                            <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                        </a>

                        <div class="item-content">
                            <h5 class="mt-10 mb-30 ajax-message"></h5>

                            <input type="hidden" name="action" value="<?php echo UPDATE_PASSWORD_ACTION; ?>"/>
                            <input type="hidden" name="id" value="<?php echo $user->id; ?>"/>

                            <label>Old Password</label>
                            <input type="password" data-ajax-field="password" placeholder="Old Password" name="old_password" value=""/>

                            <label>New Password</label>
                            <input type="password" data-ajax-field="password" placeholder="New Password" name="new_password" value=""/>

                            <label>Confirm Password</label>
                            <input type="password" data-ajax-field="password" placeholder="Confirm Password" name="confirm_password" value=""/>

                            <div class="btn-wrapper"><button type="submit" class="c-btn mb-10"><b>Update</b></button></div>
                        </div><!--item-content-->
                    </form>
                <?php } ?>

            </div><!--room-form-->
        </div><!--main-content-inner-->

    </div><!--container-->

    <?php echo "<script>var currentAction = '" .  PROFILE_ACTION  . "'</script>"; ?>
    <?php echo "<script>var userInfo = '" .  USER_INFO  . "'</script>"; ?>

    
</main>

<?php require("common/php/php-footer.php"); ?>

<script>
    
    /*MAIN SCRIPTS*/
    
    (function ($) {
        "use strict";

        loadProfile();

    })(jQuery);

</script>

</body>

</html>

