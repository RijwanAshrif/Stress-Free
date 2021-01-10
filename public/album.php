<?php require_once('../private/init.php');

$user = Session::get_session(new User());
$logged_in = (!empty($user)) ? true : false;
$album_id = Helper::get_val("id");

require("common/php/php-head.php"); ?>

<main>

    <div class="container ">
        <div class="mt-50 row" id="albums"></div>

        <div id="album-detail" class="mb-50"></div>

        <div id="album-tracks" class="white-playlist"></div>
    </div>


    <?php echo "<script>currentAction = '" .  ALBUMS_ACTION  . "'</script>"; ?>

    <?php if($album_id){
        echo "<script>albumId = '" .  $album_id  . "'</script>";
    }else{
        echo "<script>albumId = null</script>";
    }?>

    <script>
        if(albumId != null) var paginationElem = '#album-tracks';
        else var paginationElem = '#albums';

        var pagination = 1,
            reachAtTheEnd = false;
    </script>

</main>


<?php require("common/php/php-footer.php"); ?>


<script>


    /*MAIN SCRIPTS*/



    (function ($) {
        "use strict";

        loadAlbums(pagination, albumId);
        
        $(window).on('scroll', function(e){
            if(!reachAtTheEnd){
                if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                    pagination++;

                    loadAlbums(pagination, albumId);
                }
            }
        });



    })(jQuery);

</script>

</body>

</html>

