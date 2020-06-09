<?php

include_once("include/password.inc.php");
include_once("include/database.inc.php");
include_once("include/symbols.inc.php");
include_once("include/active-users.inc.php");


goHomeOnWrongPassword();

$someone_chatting = false;
$someone_on_video = false;
$someone_on_mobile = false;

$users = get_users_active();

foreach ($users as $name => $options)
{
	echo '<span class=active-user>';
	echo non_breaking_username($name);

	if ($options["chatting"])
	{
		echo "&nbsp;" . symbolSpeech();
		$someone_chatting = true;
	}

	if ($options["video"])
	{
		echo "&nbsp;" . symbolCinema();
		$someone_on_video = true;
	}

	if ($options["mobile"])
	{
		echo "&nbsp;" . symbolMobilePhone();
		$someone_on_mobile = true;
	}

	echo '</span>';
	echo ' ';
}

if ($someone_chatting || $someone_on_video || $someone_on_mobile)
{
	echo "<p>";
}

if ($someone_chatting)
{
	echo symbolSpeech() . " = using text chat ";
}

if ($someone_on_video)
{
	echo symbolCinema() . " = in a video room ";
}

if ($someone_on_mobile)
{
	echo symbolMobilePhone() . " = on a mobile device";
}
