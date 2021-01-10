<?php require_once('../../private/init.php'); ?>

<?php



$errors = Session::get_temp_session(new Errors());
$admin = Session::get_session(new Admin());

if(!empty($admin)) Helper::redirect_to("index.php");
else $admin = new Admin();

?>

<?php require("common/php/php-head.php"); ?>

<body>

<div class="dplay-tbl">
	<div class="dplay-tbl-cell">
		<div class="item-wrapper one pb-100">
			<div class="item">
				<div class="item-inner">
					<h4 class="item-header">Login</h4>



					<div class="item-content">

                        <div class="ajax-bar"></div>

						<form class="login-form" method="post" data-url="<?php echo ADMIN_LOGIN_API; ?>">

							<div class="btn-loader loader-big"><span class=" ajax-loader"><span></span></span></div>

							<h5 class="mt-10 mb-30 ajax-message"></h5>

							<label>Email</label>
							<input data-ajax-field="email" type="text" class="form-control" name="email" placeholder="Email">

							<label>Password</label>
							<input data-ajax-field="true" type="password" class="form-control" name="password" placeholder="Password">

							<div class="btn-wrapper"><button type="submit" class="c-btn mb-10"><b>Login</b></button></div>

						</form>
					</div><!--item-content-->
				</div><!--item-inner-->
			</div><!--item-->
		</div><!--item-wrapper-->
	</div><!--dplay-tbl-cell-->
</div><!-- dplay-tbl -->

<!-- jQuery library -->
<script src="plugin-frameworks/jquery-3.2.1.min.js"></script>

<!-- Main Script -->
<script src="common/other/script.js"></script>

<script>


	/*MAIN SCRIPTS*/
	(function ($) {
		"use strict";

        $('.login-form').on('submit', function(e){
            e.stopPropagation();
            e.preventDefault();

            var $this = $(this),
                wrapperClass = $this.closest('.item-content'),
                ajaxBar = wrapperClass.find('.ajax-bar'),
                url = $this.data('url');

            if(validateForm($this)) {

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $this.serialize(),
                    dataType : 'json',
                    beforeSend: function(e){
                        $(ajaxBar).addClass('active').css('width', 0 +  '%');
                        bigLoaderEnable(wrapperClass);
                        renderTableMessage(wrapperClass, '', true);
                    },
                    error: function(err) {
                        bigLoaderDisable(wrapperClass);
                        if(err.status == 404) renderTableMessage(wrapperClass, 'Invalid Api.', true);
                        else renderTableMessage(wrapperClass, 'Something went wrong. Please try again.', true);
                    },
                    success: function(response) {

                        var uploadedObj = JSON.parse(JSON.stringify(response));
                        if (uploadedObj.status_code == 200) {
                            renderFormMessage(wrapperClass, uploadedObj.message, false);

                            location.reload();

                        }else renderFormMessage(wrapperClass, uploadedObj.message, true);

                        bigLoaderDisable(wrapperClass);
                    },
                    xhr: function(){
                        var xhr = $.ajaxSettings.xhr();
                        if (xhr.upload) {
                            xhr.upload.addEventListener('progress', function(event) {
                                var percent = 0;
                                var position = event.loaded || event.position;
                                var total = event.total;
                                if (event.lengthComputable) {

                                    percent = Math.ceil(position / total * 100);
                                    $(ajaxBar).css('width', percent +  '%');
                                }
                            }, true);
                        }
                        return xhr;
                    },
                    mimeType:"multipart/form-data"
                });
            }
        });
        
	})(jQuery);
    
</script>


</body>
</html>