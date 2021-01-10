<?php

$current = basename($_SERVER["SCRIPT_FILENAME"]);
$b_index = $b_users = $b_admob = $b_site_config = "";
$b_property = $b_artist = $b_genre = $b_album = $b_track = $b_playlist = $b_push_notification = $b_add_feedback = "";

if($current == "index.php") $b_index = "active";
else if(($current == "artists.php") ||($current == "artist-form.php") || ($current == "artist-tracks.php")) $b_artist = "active";
else if(($current == "genres.php") ||($current == "genre-form.php") || ($current == "genre-tracks.php")) $b_genre = "active";
else if(($current == "albums.php") || ($current == "album-form.php") || ($current == "album-tracks.php")) $b_album = "active";
else if(($current == "push-messages.php") || ($current == "notification-form.php")) $b_push_notification = "active";
else if(($current == "tracks.php") ||($current == "track-form.php")) $b_track = "active";
else if($current == "users.php") $b_users = "active";
else if($current == "app-feedback.php") $b_add_feedback = "active";
else if($current == "playlists.php" || $current == "playlist-tracks.php" || $current == "playlist-form.php") $b_playlist = "active";
else if($current == "admob.php") $b_admob = "active";
else if($current == "site-config.php") $b_site_config = "active";

?>

<div class="sidebar" id="sidebar">
    <ul class="sidebar-list">
              <li class="<?php echo $b_index; ?>"><a href="index.php"><i class="ion-ios-pie"></i><span>Dashboard</span></a></li>
        <li class="<?php echo $b_artist; ?>"><a href="artists.php"><i class="ion-android-person"></i><span>Music Artists</span></a></li>
        <li class="<?php echo $b_genre; ?>"><a href="genres.php"><i class="ion-android-apps"></i><span>Music Genres</span></a></li>
        <li class="<?php echo $b_album; ?>"><a href="albums.php"><i class="ion-ios-albums"></i><span>Music Albums</span></a></li>
        <li class="<?php echo $b_track; ?>"><a href="tracks.php"><i class="ion-ios-musical-notes"></i><span>Tracks</span></a></li>
            <li class="<?php echo $b_site_config; ?>"><a href="site-config.php"><i class="ion-settings"></i><span>Configuration</span></a></li>

	</ul>
</div><!--sidebar-->