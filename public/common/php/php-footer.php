



<footer>
    <div class="container">
        <div class="row">

            <div class="col-sm-5">

                <a class="logo not-load" data-page="home" data-title="Home" href="index.php"><img src="images/logo_black.png" alt=""></a>
                <p class="mt-15 mb-30">StreeFree is a part of Relaxation. As our goal is to help people feel relax & strees free, we will try to develop our website at place where they see most. We will try to satisfy almost all major demands for relaxation with sound by our webapp.</p>

            </div><!--col-sm-4-->

            <div class="col-sm-3">

                <h4 class="mb-15"><b>Userful links</b></h4>
                <ul class="mb-30">
                    <li><a href="about-us.php">About Us</a></li>
                    <li><a href="privacy-policy.php">Privacy policy</a></li>
                    <li><a href="terms-conditions.php">Terms & Conditions</a></li>
                </ul>

            </div><!--col-sm-4-->

            <div class="col-sm-4">

                <h4 class="mb-15"><b>Contact</b></h4>

                <h5 class="mb-10"><a href="mailto:someone@example.com">streefree@gmail.com</a></h5>
                <h5 class="mb-15"><a href="tel:123-456-7890">01631501648</a></h5>

                <ul class="social-icons mb-30">
                    <li><a href="#"><i class="ion-social-facebook"></i></a></li>
                    <li><a href="#"><i class="ion-social-twitter"></i></a></li>
                    <li><a href="#"><i class="ion-social-linkedin"></i></a></li>
                    <li><a href="#"><i class="ion-social-instagram"></i></a></li>
                </ul>

            </div><!--col-sm-4-->

        </div><!--row-->

        <p class="copyright">Copyright © Stree Free</p>

    </div><!--container-->

</footer>



<div class="playlist-popup" id="my-playlist">

</div>

<?php $logged_in_user_id = $logged_in ? $user->id : 0; ?>
<?php echo "<script>serverUrl = '" . SERVER_URL . "'</script>"; ?>
<?php echo "<script>uploadedFloder = '" . ADMIN_IMAGE_LINK  . "'</script>"; ?>
<?php echo "<script>isLoggedIn = '" .  $logged_in  . "'</script>"; ?>
<?php echo "<script>loggedInUserID = '" .  $logged_in_user_id  . "'</script>"; ?>
<?php echo "<script>currentAPI = '" .  MAIN_API  . "'</script>"; ?>
<?php echo "<script>songDownloadAction  = '" .  SONG_DOWNLOAD_ACTION  . "'</script>"; ?>
<?php echo "<script>SongDetailAction = '" .  SONG_DETAIL_ACTION  . "'</script>"; ?>
<?php echo "<script>playlistByUserAction = '" .  PLAYLIST_BY_USER_ACTION  . "'</script>"; ?>
<?php echo "<script>createPlaylistAction = '" .  CREATE_PLAYLIST_ACTION  . "'</script>"; ?>
<?php echo "<script>deletePlaylistAction = '" .  DELETE_PLAYLIST_ACTION  . "'</script>"; ?>
<?php echo "<script>addToPlaylistAction = '" .  ADD_TO_PLAYLIST_ACTION  . "'</script>"; ?>
<?php echo "<script>removeFromPlaylistAction = '" .  REMOVE_FROM_PLAYLIST_ACTION  . "'</script>"; ?>
<?php echo "<script>addToFavouriteAction = '" .  ADD_TO_FAVOURITE_ACTION . "'</script>"; ?>
<?php echo "<script>removeFavouriteAction = '" .  REMOVE_FAVOURITE_ACTION . "'</script>"; ?>
<?php echo "<script>trackDetailAction = '" .  TRACK_DETAIL_ACTION . "'</script>"; ?>

<?php echo "<script>savePlaylistAction = '" .  SAVE_PLAYLIST_ACTION . "'</script>"; ?>
<?php echo "<script>unSavePlaylistAction = '" .  UN_SAVE_PLAYLIST_ACTION . "'</script>"; ?>

<?php echo "<script>deafultPlaylistImage = '" .  DEFAULT_PLAYLIST_IMAGE . "'</script>"; ?>
<?php echo "<script>defaultImage = '" .  BEFORE_LOAD_IMAGE . "'</script>"; ?>
<?php echo "<script>maxUploadedFile = '" . MAX_IMAGE_SIZE  . "'</script>"; ?>

<?php echo "<script>searchDataAction = '" .  SEARCHED_TERMS_ACTION . "'</script>"; ?>
<?php echo "<script>downloadAction = '" .  DOWNLOAD_ACTION . "'</script>"; ?>
<?php echo "<script>downloadLinkActiveTiming = '" .  DOWNLOAD_LINK_ACTIVE_TIMING . "'</script>"; ?>


<?php echo "<script>homePageTitle = '" .  $page_title . "'</script>"; ?>
<?php echo "<script>registrationMessage = '" .  REGISTRATION_MESSAGE . "'</script>"; ?>
<?php echo "<script>nothingFound = '" .  NOTHING_FOUND . "'</script>"; ?>
<?php echo "<script>cantBeEmpty = '" .  CANT_BE_EMPTY . "'</script>"; ?>

<?php echo "<script>downloadActiveTimeMessage = '" .  DOWNLOAD_ACTIVE_TIME_MESSAGE . "'</script>"; ?>
<?php echo "<script>noPlaylistMessage = '" .  NO_PLAYLIST_MESSAGE . "'</script>"; ?>

<?php echo "<script>somethingWentWrongMessage = '" .  SOMETHING_WENT_WRONG_MESSAGE . "'</script>"; ?>
<?php echo "<script>requiredMessage = '" .  REQUIRED_MESSAGE . "'</script>"; ?>
<?php echo "<script>invalidEmailMEessage = '" .  INVALID_EMAIL_MESSAGE . "'</script>"; ?>
<?php echo "<script>minPassLength = '" .  MIN_PASSWORD_LENGTH . "'</script>"; ?>
<?php echo "<script>minPassMessage = '" .  MIN_PASSWORD_MESSAGE . "'</script>"; ?>
<?php echo "<script>radionRequiredError = '" .  RADIO_REQUIRED_ERROR . "'</script>"; ?>
<?php echo "<script>numericError = '" .  NUMERIC_ERROR . "'</script>"; ?>
<?php echo "<script>shuffleOnMessage = '" .  SHUFFLE_ON_MESSAGE . "'</script>"; ?>
<?php echo "<script>shuffleOffMessage = '" .  SHUFFLE_OFF_MESSAGE . "'</script>"; ?>
<?php echo "<script>repeatOnMessage = '" .  REPEAT_ON_MESSAGE . "'</script>"; ?>
<?php echo "<script>repeatOffMessage = '" .  REPEAT_OFF_MESSAGE . "'</script>"; ?>
<?php echo "<script>imageMaxSizeMessage = '" .  IMAGE_MAX_SIZE_MESSAGE . "'</script>"; ?>
<?php echo "<script>imageInvalidMessage = '" . IMAGE_INVALID_MESSAGE . "'</script>"; ?>
<?php echo "<script>invalidApiMessage = '" . INVALID_API_MESSAGE . "'</script>"; ?>
<?php echo "<script>increaseListeningCountAction = '" . INCREASE_LISTENING_COUNT_ACTION . "'</script>"; ?>


<script>
    
    var swiperSlider = null;
    
    var trackList = JSON.parse(localStorage.getItem("track_list"));
    if(trackList == null) trackList = [];

    var currentPlaying = JSON.parse(localStorage.getItem("current_playing"));
    if(currentPlaying == null) currentPlaying = null;

    var trackDetails = JSON.parse(localStorage.getItem("track_detail"));
    if(trackDetails == null) trackDetails = { currentPlayingTime: 0, isPlaying : false, };
    
</script>


<script src="plugin-frameworks/jquery-3.4.1.min.js"></script>
<script src="plugin-frameworks/jquery.history.js"></script>
<script src="plugin-frameworks/swiper.min.js"></script>
<script src="common/other/script.js"></script>
