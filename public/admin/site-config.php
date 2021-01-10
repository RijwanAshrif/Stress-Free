<?php require_once('../../private/init.php'); ?>

<?php

$errors = Session::get_temp_session(new Errors());
$message = Session::get_temp_session(new Message());
$admin = Session::get_session(new Admin());

if(empty($admin)) Helper::redirect_to("login.php");

?>

<?php require("common/php/php-head.php"); ?>

<body>

<?php require("common/php/header.php"); ?>

<div class="main-container">

	<?php require("common/php/sidebar.php"); ?>

	<div class="main-content">

		<div class="main-content-inner">


			<ul class="ajax-sidebar">
				<li class="head">Configuration</li>
				<li><a href="#<?php echo CONFIG_SITE; ?>">Site Configuration</a></li>
				
			</ul>

			<div class="ajax-form-wrapper loader-wrapper">
				<div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>

				<form class="ajax-form tab-form firebase_push" id="<?php echo CONFIG_PUSH_NOTIFICATION; ?>"
					  method="post" data-url="<?php echo CONFIG_PUSH_NOTIFICATION_API; ?>">

					<input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>
					<a href="#" class="head ajax-sidebar-dropdown">
						<h5 class="title">Push Notification</h5>
						<span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
					</a>

					<div class="item-content">
						<div class="ajax-bar"></div>

						<h5 class="mt-10 mb-30 ajax-message"></h5>

						<input type="hidden" name="id" value=""/>

						<label>AuthKey</label>
						<textarea rows="4" cols="50" data-ajax-field="true" placeholder="Firebase Auth key" name="firebase_auth"></textarea>

						<div class="btn-wrapper"><button type="submit" class="demo-disable c-btn mb-10"><b>Update</b></button></div>
					</div><!--item-content-->
				</form>



				<form class="ajax-form tab-form site_config" id="<?php echo CONFIG_SITE; ?>"
					  method="post" data-url="<?php echo CONFIG_SITE_API; ?>">

					<input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>
					<a href="#" class="head ajax-sidebar-dropdown">
						<h5 class="title">Site Configuration</h5>
						<span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
					</a>

					<div class="item-content">
						<div class="ajax-bar"></div>

						<h5 class="mt-10 mb-30 ajax-message"></h5>

						<input type="hidden" name="id" value=""/>

						<label class="control-label" for="file">Logo(<?php echo "Max Image Size : " . MAX_IMAGE_SIZE . "MB. Required Format : png/jpg/jpeg"; ?>)</label>

						<div class="image-upload">
							<div class="dplay-tbl">
								<div class="dplay-tbl-cell">
									<img data-logo-element=".site-logo" class="max-h-200x uploaded-image site_config image_name" alt="" src=""/>
								</div>
							</div>

							<input data-url="<?php echo CONFIG_IMAGE_API; ?>" type="file" class="ajax-img-upload" name="image_name" />
						</div><!--image-upload-->

						<label>Site Title</label>
						<input type="text" data-ajax-field="true" placeholder="Site Title" name="title" value=""/>

						<label>Site Tag Line</label>
						<input type="text" data-ajax-field="true" placeholder="Site Tag Line" name="tag_line" value="" />

						<div class="btn-wrapper"><button type="submit" class="c-btn mb-10"><b>Update</b></button></div>
					</div><!--item-content-->
				</form>


				<form class="ajax-form tab-form admin_token" id="<?php echo CONFIG_API_TOKEN; ?>"
					  method="post" data-url="<?php echo CONFIG_API_TOKEN_API; ?>">

					<input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>
					<a href="#" class="head ajax-sidebar-dropdown">
						<h5 class="title">API Token</h5>
						<span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
					</a>

					<div class="item-content">
						<div class="ajax-bar"></div>

						<h5 class="mt-10 mb-30 ajax-message"></h5>

						<input type="hidden" name="id" value=""/>

						<label>Api Token(To secure API)</label>
						<input data-ajax-field="true" type="text" placeholder="eg. etr@2wuenfe3r@" name="admin_token" value="" />

						<div class="btn-wrapper"><button type="submit" class="c-btn mb-10"><b>Update</b></button></div>
					</div><!--item-content-->
				</form>


				<form class="ajax-form tab-form smtp_config" id="<?php echo CONFIG_SMTP; ?>"
					  method="post" data-url="<?php echo CONFIG_SMTP_API; ?>">

					<input type="hidden" name="id" value="<?php echo Helper::get_val("id"); ?>"/>
					<a href="#" class="head ajax-sidebar-dropdown">
						<h5 class="title">SMTP Configuration</h5>
						<span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
					</a>

					<div class="item-content">
						<div class="ajax-bar"></div>

						<h5 class="mt-10 mb-30 ajax-message"></h5>

						<input type="hidden" name="id" value=""/>

						<label>Host</label>
						<input data-ajax-field="true" type="text" placeholder="eg. smtp.gmail.com" name="host" value="">

						<label>Sender Email</label>
						<input data-ajax-field="true" type="text" placeholder="eg. doe@gmail.com" name="sender_email" value="">

						<label>Username</label>
						<input data-ajax-field="true" type="text" placeholder="eg. abc" name="username" value="">

						<label>Password</label>
						<input data-ajax-field="true" type="password" placeholder="eg. password" name="smtp_password" value="">

						<div class="input-6 pr-7-5">
							<label>Port</label>
							<input data-ajax-field="true" type="text" placeholder="eg. 465" name="port" value="">
						</div>

						<div class="input-6 pl-7-5">
							<label>Encryption</label>
							<input data-ajax-field="true" type="text" placeholder="eg. tls" name="encryption" value="">
						</div>

						<div class="btn-wrapper"><button type="submit" class="c-btn mb-10"><b>Update</b></button></div>
					</div><!--item-content-->
				</form>

			</div><!--room-form-->
		</div><!--main-content-inner-->

	</div><!--main-content-->
</div><!--main-container-->

<script>
    var countryCurrency = '<?php echo CURRENCY_FONT_API; ?>';
</script>


<?php echo "<script>uploadedLink = '" . ADMIN_IMAGE_LINK . "'</script>"; ?>
<?php echo "<script>uploadedThumbLink = '" . ADMIN_THUMB_LINK . "'</script>"; ?>
<?php echo "<script>maxUploadedFile = '" . MAX_IMAGE_SIZE  . "'</script>"; ?>

<?php require("common/php/php-footer.php"); ?>

<script>

	/*MAIN SCRIPTS*/
	(function ($) {
		"use strict";

		ajaxSidebarInit(window.location.hash, '<?php echo CONFIG_SITE; ?>');

	})(jQuery);

</script>

