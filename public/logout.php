<?php require_once('../private/init.php'); ?>

<?php

Session::unset_session(new User());
Helper::redirect_to("index.php");

?>