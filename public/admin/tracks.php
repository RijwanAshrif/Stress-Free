<?php require_once('../../private/init.php'); ?>
<?php

$admin = Session::get_session(new Admin());

$page = Helper::get_val("page");
if(!$page) $page = 1;

$search = Helper::get_val("search");
if(!$search) $search = null;

$sort = Helper::get_val("sort");
if(!$sort) $sort = null;

if(empty($admin)) Helper::redirect_to("login.php"); ?>


<?php require("common/php/php-head.php"); ?>

<body>

<div class="lyrics-container">
    <h5 class="mt-10 mb-20 ajax-message"></h5>
    <div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>
    <form class="lyrics-form">
        <h4>Lyrics</h4>

        <input type="hidden" name="id"/>
        <div class="lyrics-body">
            <textarea data-ajax-field="wysiwyg" type="text" placeholder="Lyrics" name="lyrics" ></textarea>
        </div>

        <div class="lyrics-footer">
            <button class="c-btn" type="submit">Save</button>
        </div>
    </form>

</div><!--lyrics-container-->


<?php require("common/php/header.php"); ?>

    <div class="main-container">

        <?php require("common/php/sidebar.php"); ?>

        <div class="main-content">
            
            <div class="oflow-hidden mb-xs-0">
                <div class="float-l search-wrapper">

                    <form method="get" class="search-form">
                        <input type="text" placeholder="Search Here" name="search" value="<?php echo $search; ?>"/>
                        <button type="submit"><b>Search</b></button>
                    </form>
                </div>
                <h6 class="float-r mt-5"><b><a class="c-btn" href="track-form.php">+ New Track</a></b></h6>
            </div>


            <div class="item-wrapper loader-wrapper p-10">

                <div class="btn-loader active loader-big"><span class="active ajax-loader"><span></span></span></div>
                <h5 class="ajax-message mb-20"></h5>

                <h5 class="mb-20 current-item"></h5>

                <table data-edit-link="track-form.php?id=" data-delete-link="<?php echo TRACK_DELETE_API . '?id='; ?>">
                    <thead>

                    </thead>
                    <tbody>

                    </tbody>
                </table>

                <div class="pagination"></div>
            </div><!--item-wrapper-->


        </div><!--main-content-->
    </div><!--main-container-->

<?php
    $playlist_id = Helper::get_val("playlist_id");
    $is_playlist = ($playlist_id) ? true : false;
?>
<?php echo "<script>maxUploadedFile = '" . MAX_IMAGE_SIZE  . "'</script>"; ?>
<?php echo "<script>maxUploadedFileCount = '" . MAX_FILE_COUNT  . "'</script>"; ?>
<?php echo "<script>uploadedLink = '" . ADMIN_IMAGE_LINK . "'</script>"; ?>
<?php echo "<script>uploadedThumbLink = '" . ADMIN_THUMB_LINK . "'</script>"; ?>
<?php echo "<script>defaultImage = '" . DEFAULT_IMAGE . "'</script>"; ?>
<?php echo "<script>isPlaylist = '" . $is_playlist . "'</script>"; ?>

<?php require("common/php/php-footer.php"); ?>


<script>

    /*MAIN SCRIPTS*/
    (function ($) {
        "use strict";

        var getAllAPI = '<?php echo TRACK_ALL_API; ?>';

        console.log(getAllAPI);

        var sort = '<?php echo $sort; ?>',
            sortType = "DESC";

        ajaxGetAll(getAllAPI, '<?php echo $page; ?>', '<?php echo $search; ?>', sort);

        $(document).on('click', '.pagination a[data-page]', function(e){
            e.preventDefault();
            e.stopPropagation();

            var $this = $(this),
                page = $this.data('page'),
                search = $('.search-form').find('[name="search"]').val();

            if(search && sort) window.history.pushState('', '', '?search=' + search + '&sort=' + sort + '&page=' + page);
            else if(search) window.history.pushState('', '', '?search=' + search + '&page=' + page);
            else if(sort) window.history.pushState('', '', '?sort=' + sort + '&page=' + page);
            else window.history.pushState('', '', '?page=' + page);

            ajaxGetAll(getAllAPI, page, search, sort, sortType);
        });


        $(document).on('submit', '.search-form', function(e){
            e.preventDefault();
            e.stopPropagation();

            var $this = $(this),
                page = 1,
                search = $this.find('[name="search"]').val();

            if(search && sort) window.history.pushState('', '', '?search=' + search + '&sort=' + sort + '&page=' + page);
            else if(search) window.history.pushState('', '', '?search=' + search + '&page=' + page);
            else if(sort) window.history.pushState('', '', '?sort=' + sort + '&page=' + page);
            else window.history.pushState('', '', '?page=' + page);

            ajaxGetAll(getAllAPI, page, search, sort, sortType);
        });


        $(document).on('click', '[data-sort]', function(e){
            e.preventDefault();
            e.stopPropagation();

            var $this = $(this),
                page = 1,
                search = $('.search-form').find('[name="search"]').val();

            sort = $this.data('sort');
            sortType = $this.data('sort-type');

            if(sortType == 'DESC') $($this).data('sort-type', 'ASC');
            else $($this).data('sort-type', 'DESC');

            if(search && sort) window.history.pushState('', '', '?search=' + search + '&sort=' + sort + '&page=' + page);
            else if(search) window.history.pushState('', '', '?search=' + search + '&page=' + page);
            else if(sort) window.history.pushState('', '', '?sort=' + sort + '&page=' + page);
            else window.history.pushState('', '', '?page=' + page);

            $this.find('i').toggleClass('sort-desc');

            ajaxGetAll(getAllAPI, page, search, sort, sortType);
        });


        $('.lyrics-form').on('submit', function(e){
            e.preventDefault();
            e.stopPropagation();

            var $this = $(this),
                lyricContainer = $('.lyrics-container'),
                lyricForm = $('.lyrics-form');

            $(lyricContainer).addClass('active');

            $.ajax({
                url: '<?php echo TRACK_LYRICS_API; ?>',
                type: 'POST',
                data: $this.serialize(),
                dataType : 'json',
                error: function(err) { bigLoaderDisable($(lyricContainer)); },
                beforeSend: function(){ bigLoaderEnable($(lyricContainer)); },
                success: function(response) {

                    $(lyricForm).addClass('active');

                    console.log(response);
                    bigLoaderDisable($(lyricContainer));

                    var uploadedObj = JSON.parse(JSON.stringify(response));
                    if (uploadedObj.status_code == 200) {

                        $(lyricContainer).removeClass('active');
                        $(lyricForm).removeClass('active');

                    }else renderTableMessage(lyricContainer, uploadedObj.data.message, false);

                },
            });

        });


        $(document).on('click', '.lyrics-link', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $this = $(this),
                trackId = $this.data('id'),
                lyricContainer = $('.lyrics-container'),
                lyricForm = $('.lyrics-form');

            $(lyricContainer).addClass('active');

            $.ajax({
                url: '<?php echo TRACK_LYRICS_API; ?>',
                type: 'GET',
                data: { id : trackId, },
                dataType : 'json',
                error: function(err) { bigLoaderDisable($(lyricContainer)); },
                beforeSend: function(){ bigLoaderEnable($(lyricContainer)); },
                success: function(response) {

                    $(lyricForm).addClass('active');

                    console.log(response);
                    bigLoaderDisable($(lyricContainer));

                    var uploadedObj = JSON.parse(JSON.stringify(response));
                    if (uploadedObj.status_code == 200) {

                        $.each(uploadedObj.data.track['text'], function(key, value){
                            $('[name="' + key + '"]').val(value);
                        });

                        var wshywyg = uploadedObj.data.track['wshywyg'];

                        for (var key in wshywyg) {


                            var currentElement = $('[name="' + key + '"]');
                            $(currentElement).val(decodeEntities(wshywyg[key]));

                            if(wshywyg[key] == null) $(currentElement).trumbowyg('html', '');
                            else $(currentElement).trumbowyg('html', decodeEntities(wshywyg[key]));
                        }

                    }else alert(response.message);

                },
            });
        });


        $(document).on('click', function(e) {
            var container = $('.lyrics-form');

            if (!container.is(e.target) && container.has(e.target).length === 0) {
                $('.lyrics-container').removeClass('active');
            }
        });





    })(jQuery);

</script>

