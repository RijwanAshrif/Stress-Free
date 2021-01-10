<?php require_once('../private/init.php');

$user = Session::get_session(new User());
$logged_in = (!empty($user)) ? true : false;
$playlist_id = Helper::get_val("id");

require("common/php/php-head.php"); ?>

<main>

    <div class="container ">

        <div class="row mt-50" id="playlists"></div>

        <div id="playlist-detail" class=" mb-50"></div>

        <div id="playlist-tracks" class="white-playlist"></div>

    </div>


    <?php echo "<script>currentAction = '" .  PLAYLISTS_ACTION  . "'</script>"; ?>

    <?php if($playlist_id){
        echo "<script>playlistId = '" .  $playlist_id  . "'</script>";
    }else{
        echo "<script>playlistId = null</script>";
    }?>

    <script>
        if(playlistId != null) var paginationElem = '#playlist-tracks';
        else var paginationElem = '#playlists';

        var pagination = 1,
            reachAtTheEnd = false;
    </script>

</main>

<?php require("common/php/php-footer.php"); ?>

<script>

    /*MAIN SCRIPTS*/

    (function ($) {
        "use strict";

        loadPlaylists(pagination, playlistId);

        $(window).on('scroll', function(e){

            if(!reachAtTheEnd){
                if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                    pagination++;

                    loadPlaylists(pagination, playlistId);
                }
            }
        });

    })(jQuery);

</script>

</body>

</html>

