<?php

include_once("include/password.inc.php");
include_once("include/database.inc.php");
include_once("include/active-users.inc.php");


goHomeOnWrongPassword();


$users = get_users_active_earlier();

foreach ($users as $name)
{
	echo '<span class=inactive-user>';
	echo non_breaking_username($name);
	echo '</span>';
	echo " ";
}
