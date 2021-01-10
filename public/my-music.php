<?php require_once('../private/init.php');

$user = Session::get_session(new User());
$logged_in = (!empty($user)) ? true : false;

if(!$logged_in) Helper::redirect_to("index.php");

require("common/php/php-head.php"); ?>


<main>

    <div class="container ">

        <div class="inactive">
            <div class="pb-20 mt-50 border-b">
                <h4 id="my-playlist-title" class="mb-30">My Playlist</h4>
                <div class="row" id="my-playlist"></div>
            </div>
        </div>

        <div class="inactive">
            <div class="pb-20 mt-50 border-b">
                <h4 id="saved-playlist-title" class="mb-30">Saved Playlist</h4>
                <div id="saved-playlist" class="row"></div>
            </div>
        </div>

        <div class="inactive">
            <div class="mt-50">
                <h4 id="favourite-tracks-title" class="mb-30">Favourite Tracks</h4>
                <div id="favourite-tracks" class="white-playlist"></div>
            </div>
        </div>

    </div>

    <?php echo "<script>currentAction = '" .  MY_MUSIC_ACTION  . "'</script>"; ?>

</main>

<?php require("common/php/php-footer.php"); ?>

<script>

    /*MAIN SCRIPTS*/

    (function ($) {
        "use strict";

        loadMyMusic(loggedInUserID);
        
    })(jQuery);

</script>

</body>

</html>

