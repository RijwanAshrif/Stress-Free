<?php require_once('../private/init.php');

$user = Session::get_session(new User());
$logged_in = (!empty($user)) ? true : false;

$genre_id = Helper::get_val("id");

require("common/php/php-head.php"); ?>


<main>

    <div class="container ">

        <div id="genre-detail" class="mt-50  mb-50"></div>

        <div id="genre-tracks" class="white-playlist"></div>

    </div>


    <?php echo "<script>currentAction = '" .  GENRES_ACTION  . "'</script>"; ?>

    <?php if($genre_id){
        echo "<script>genreId = '" .  $genre_id  . "'</script>";
    }else{
        echo "<script>genreId = null</script>";
    }?>

    <script>
        if(genreId != null) var paginationElem = '#genre-tracks';
        else var paginationElem = '#genres';

        var pagination = 1,
            reachAtTheEnd = false;
    </script>

</main>


<?php require("common/php/php-footer.php"); ?>

<script>

    
    /*MAIN SCRIPTS*/

    (function ($) {
        "use strict";

        loadGenres(pagination, genreId);

        $(window).on('scroll', function(e){

            if(!reachAtTheEnd){
                if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                    pagination++;

                    loadGenres(pagination, genreId);
                }
            }
        });


    })(jQuery);

</script>

</body>

</html>

