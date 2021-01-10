<?php require_once('../private/init.php');

$user = Session::get_session(new User());
$logged_in = (!empty($user)) ? true : false;

$tag_id = Helper::get_val("id");

require("common/php/php-head.php"); ?>


<main>

    <div class="container">

        <!--<div class="mt-50 link-item" id="tags"></div>-->

        <div id="tag-detail" class="mt-50  mb-50"></div>

        <div id="tag-tracks" class="white-playlist"></div>

    </div>


    <?php echo "<script>currentAction = '" .  TAGS_ACTION  . "'</script>"; ?>

    <?php if($tag_id){
        echo "<script>tagId = '" .  $tag_id  . "'</script>";
    }else{
        echo "<script>tagId = null</script>";
    }?>


    <script>
        if(tagId != null) var paginationElem = '#tag-tracks';
        else var paginationElem = '#tags';

        var pagination = 1,
            reachAtTheEnd = false;
    </script>

</main>

<?php require("common/php/php-footer.php"); ?>


<script>

    /*MAIN SCRIPTS*/

    (function ($) {
        "use strict";

        loadTags(pagination, tagId);

        $(window).on('scroll', function(e){

            if(!reachAtTheEnd){
                if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                    pagination++;

                    loadTags(pagination, tagId);
                }
            }
        });

    })(jQuery);

</script>

</body>

</html>

