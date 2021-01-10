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
        </div>


        <div class="item-wrapper loader-wrapper p-10">

            <div class="btn-loader active loader-big"><span class="active ajax-loader"><span></span></span></div>
            <h5 class="ajax-message mb-20"></h5>

            <h5 class="mb-20 current-item"></h5>

            <table data-delete-link="<?php echo APP_FEEDBACK_DELETE_API . '?id='; ?>">
                <thead>

                </thead>
                <tbody>

                </tbody>
            </table>

            <div class="pagination"></div>
        </div><!--item-wrapper-->


    </div><!--main-content-->
</div><!--main-container-->

<?php echo "<script>maxUploadedFile = '" . MAX_IMAGE_SIZE  . "'</script>"; ?>
<?php echo "<script>maxUploadedFileCount = '" . MAX_FILE_COUNT  . "'</script>"; ?>
<?php echo "<script>uploadedLink = '" . ADMIN_IMAGE_LINK . "'</script>"; ?>
<?php echo "<script>uploadedThumbLink = '" . ADMIN_THUMB_LINK . "'</script>"; ?>
<?php echo "<script>defaultImage = '" . DEFAULT_IMAGE . "'</script>"; ?>

<?php require("common/php/php-footer.php"); ?>


<script>

    /*MAIN SCRIPTS*/
    (function ($) {
        
        "use strict";

        var getAllAPI = '<?php echo APP_FEEDBACK_ALL_API; ?>',
            sort = '<?php echo $sort; ?>',
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

    })(jQuery);

</script>

