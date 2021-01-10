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
				<li class="head">Admin Profile</li>
				<li><a href="#<?php echo ADMIN_INFO; ?>">General Info</a></li>
				<li><a href="#<?php echo ADMIN_CREDENTIAL; ?>">Update Profile</a></li>
				<li><a href="#<?php echo ADMIN_PASSWORD; ?>">Update Password</a></li>
			</ul>

			<div class="ajax-form-wrapper loader-wrapper">
				<div class="btn-loader loader-big"><span class="active ajax-loader"><span></span></span></div>

				<form class="ajax-form tab-form" id="<?php echo ADMIN_INFO; ?>" method="post"
					  data-url="<?php echo ADMIN_INFO_API; ?>">

					<input type="hidden" name="id" value=""/>

					<a href="#" class="head ajax-sidebar-dropdown">
						<h5 class="title">Admin Details</h5>
						<span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
					</a>

					<div class="item-content">
						<div class="ajax-bar"></div>

						<h5 class="mt-10 mb-30 ajax-message"></h5>

						<label class="control-label" for="file">Logo(<?php echo "Max Image Size : " . MAX_IMAGE_SIZE . "MB. Required Format : png/jpg/jpeg"; ?>)</label>

						<div class="image-upload">
							<div class="dplay-tbl">
								<div class="dplay-tbl-cell">
									<img class="max-h-200x uploaded-image admin image_name" alt="" src=""/>
								</div>
							</div>

							<input data-url="<?php echo ADMIN_PROFILE_IMAGE; ?>" type="file" class="ajax-img-upload" name="image_name" />
						</div><!--image-upload-->


						<div class="oflow-hidden w-100">
							<div class="input-6 pr-7-5">
								<label>First Name</label>
								<input type="text" data-ajax-field="true" placeholder="First Name" name="first_name" value=""/>
							</div>

							<div class="input-6 pl-7-5">
								<label>Last Name</label>
								<input type="text" data-ajax-field="true" placeholder="Last Name" name="last_name" value="" />
							</div>
						</div><!--oflow-hidden-->

						<label>Location</label>
						<input type="text" data-ajax-field="true" placeholder="Location" name="location" value=""/>


						<div class="oflow-hidden w-100">
							<div class="input-6 pr-7-5">
								<label>Phone</label>
								<input type="text" data-ajax-field="true" placeholder="Phone" name="phone" value=""/>
							</div>

							<div class="input-6 pl-7-5">
								<label>Speaks</label>
								<input type="text" data-ajax-field="true" placeholder="Speaks" name="speaks" value=""/>
							</div>
						</div><!--oflow-hidden-->

						<textarea data-ajax-field="wshywyg" placeholder="Admin Detail" name="description"></textarea>

						<div class="btn-wrapper"><button type="submit" class="c-btn mb-10"><b>Update</b></button></div>
					</div><!--item-content-->
				</form>


				<form class="ajax-form tab-form" id="<?php echo ADMIN_CREDENTIAL; ?>" method="post"
					  data-url="<?php echo ADMIN_CREDENTIAL_API; ?>">

					<input type="hidden" name="id" value=""/>
					<a href="#" class="head ajax-sidebar-dropdown">
						<h5 class="title">Admin Credentials</h5>
						<span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
					</a>



					<div class="item-content">

						<h5 class="mt-10 mb-30 ajax-message"></h5>

                        <input type="hidden" name="id" value=""/>

						<label>Username</label>
						<input type="text" data-ajax-field="true" placeholder="Username" name="username" value=""/>

                        <label>Email</label>
                        <input type="text" data-ajax-field="true" placeholder="Email" name="email" value=""/>

                        <label>Password</label>
                        <input type="password" data-ajax-field="true" placeholder="Password" name="password" value=""/>


						<div class="btn-wrapper"><button type="submit" class="c-btn mb-10"><b>Update</b></button></div>
					</div><!--item-content-->
				</form>


                <form class="ajax-form tab-form" id="<?php echo ADMIN_PASSWORD; ?>" method="post"
                      data-url="<?php echo ADMIN_PASSWORD_API; ?>">

                    <input type="hidden" name="id" value=""/>
                    <a href="#" class="head ajax-sidebar-dropdown">
                        <h5 class="title">Admin Credentials</h5>
                        <span class="dropdown-icon"><i class="ion-android-arrow-dropdown"></i></span>
                    </a>



                    <div class="item-content">

						<h5 class="mt-10 mb-30 ajax-message"></h5>

                        <input type="hidden" name="id" value=""/>

                        <label>Old Password</label>
                        <input type="password" data-ajax-field="true" placeholder="Old Password" name="old_password" value=""/>

                        <label>New Password</label>
                        <input type="password" data-ajax-field="true" placeholder="New Password" name="new_password" value=""/>

                        <label>Confirm Password</label>
                        <input type="password" data-ajax-field="true" placeholder="Confirm Password" name="confirm_password" value=""/>


                        <div class="btn-wrapper"><button type="submit" class="c-btn mb-10"><b>Update</b></button></div>
                    </div><!--item-content-->
                </form>

			</div><!--room-form-->
		</div><!--main-content-inner-->
		
	</div><!--main-content-->
</div><!--main-container-->


<?php require("common/php/php-footer.php"); ?>

<?php echo "<script>uploadedLink = '" . ADMIN_IMAGE_LINK . "'</script>"; ?>
<?php echo "<script>uploadedThumbLink = '" . ADMIN_THUMB_LINK . "'</script>"; ?>
<?php echo "<script>maxUploadedFile = '" . MAX_IMAGE_SIZE  . "'</script>"; ?>


<?php echo "<script>adminId = '" . $admin->id  . "'</script>"; ?>

<script>

	/*MAIN SCRIPTS*/
	(function ($) {
		"use strict";
		
		ajaxSidebarInit(window.location.hash, '<?php echo ADMIN_INFO; ?>');

	})(jQuery);

</script>
