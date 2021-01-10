<?php require_once('../private/init.php');

$user = Session::get_session(new User());
$logged_in = (!empty($user)) ? true : false;

$searched = Helper::get_val("search");

$track_id = Helper::get_val("id");




require("common/php/php-head.php"); ?>

<main>

    <div class="container ">

        <div class="inactive">
            <div id="track-detail" class="mt-50"></div>
        </div>
        
        <div id="track-list" class="mt-50 white-playlist"></div>


    </div>

    <script>

        var paginationElem = '#track-list';

        var search = null,
            trackId = null,
            pagination = 1,
            reachAtTheEnd = false;
    </script>

    <?php echo "<script>currentAction = '" .  SEARCH_ACTION . "'</script>"; ?>

    <?php if($searched){

        echo "<script>search = '" .  $searched  . "'</script>";

    }else if($track_id){

        echo "<script>trackId = '" .  $track_id  . "'</script>";

    } ?>

</main>

<?php require("common/php/php-footer.php"); ?>

<script>
    
    /*MAIN SCRIPTS*/

    (function ($) {
        "use strict";

        loadTracks(pagination, search, trackId);
        

        if(trackId == null){
            $(window).on('scroll', function(e){

                if(!reachAtTheEnd){
                    if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                        pagination++;
                        loadTracks(pagination, search, trackId);
                    }
                }
            });
        }
        
    })(jQuery);

</script>

</body>

</html>

