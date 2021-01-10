<?php require_once('../private/init.php');

$user = Session::get_session(new User());
$logged_in = (!empty($user)) ? true : false;

$artist_id = Helper::get_val("id");

require("common/php/php-head.php"); ?>


<main>

    <div class="container ">

        <div class="mt-50 row" id="popular-artists"></div>

        <div id="artist-detail" class="mb-50"></div>

        <div id="artist-tracks" class="white-playlist"></div>

    </div>


    <?php echo "<script>currentAction = '" .  ARTISTS_ACTION  . "'</script>"; ?>

    <?php if($artist_id){
        echo "<script>artistId = '" .  $artist_id  . "'</script>";
    }else{
        echo "<script>artistId = null</script>";
    }?>

    <script>
        if(artistId != null) var paginationElem = '#artist-tracks';
        else var paginationElem = '#popular-artists';

        var pagination = 1,
            reachAtTheEnd = false;
    </script>

</main>


<?php require("common/php/php-footer.php"); ?>

<script>
    
    /*MAIN SCRIPTS*/

    (function ($) {
        "use strict";

        loadArtists(pagination, artistId);

        $(window).on('scroll', function(e){
            if(!reachAtTheEnd){
                if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                    pagination++;

                    loadArtists(pagination, artistId);
                }
            }
        });
        
        
    })(jQuery);

</script>

</body>

</html>

