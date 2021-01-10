<?php require_once('../../private/init.php'); ?>

<?php
$admin = Session::get_session(new Admin());
if(empty($admin)) Helper::redirect_to("login.php");
else {
	$setting = new Setting();
	$setting = $setting->where(["admin_id"=> $admin->id])->one();
}
?>

<?php require("common/php/php-head.php"); ?>
<body>
<?php require("common/php/header.php"); ?>
<div class="main-container">

	<?php require("common/php/sidebar.php"); ?>

	<div class="main-content">
		<div class="item-wrapper three">
		

		


			<?php $tracks = new Track();
				$tracks = $tracks->where(["admin_id" => $admin->id])->count(); ?>

			<div class="item item-dahboard">
				<div class="item-inner">
					<div class="item-content">
						<h2 class="title"><b><?php echo $tracks; ?></b></h2>
						<h4 class="desc">Music</h4>
					</div>
					<div class="icon"><i class="ion-social-buffer"></i></div>
					<div class="item-footer">
						<a href="tracks.php">More info <i class="ml-10 ion-chevron-right"></i><i class="ion-chevron-right"></i></a>
					</div><!--item-footer-->
				</div><!--item-inner-->
			</div><!--item-->


			
			


			

				</div><!--item-inner-->
			</div><!--item-->
		</div><!--item-wrapper-->


	</div><!--main-content-->
</div><!--main-container-->


<?php require("common/php/php-footer.php"); ?>

