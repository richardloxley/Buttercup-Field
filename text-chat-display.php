<?php

include_once("user-input.inc.php");
include_once("database.inc.php");



function pluralise($number, $units)
{
	if ($number == 1)
	{
		return $number . " " . $units;
	}
	else
	{
		return $number . " " . $units . "s";
	}
}


function humanTimeSince($mysql_timestamp)
{
	$timestamp = strtotime($mysql_timestamp);
	$seconds = time() - $timestamp;

	if ($seconds < 60)
	{
		return "now";
	}

	$minutes = floor($seconds / 60);

	if ($minutes < 60)
	{
		return pluralise($minutes, "minute") . " ago";
	}

	$hours = floor($minutes / 60);

	if ($hours < 24)
	{
		return pluralise($hours, "hour") . " ago";
	}

	$days = floor($hours / 24);

	return pluralise($days, "day") . " ago";
}


$myNickname = getNickname();
user_is_active($myNickname);


$messages = get_text_chats(72);

foreach ($messages as $message)
{
	$posted = $message['posted'];
	$nickname = $message['nickname'];
	$text = $message['message'];

	$timestamp = strtotime($posted);
	$human_time = date("D H:i", $timestamp);

	if ($nickname == $myNickname)
	{
		// don't display name since it's me
		echo "<div class='my-text-message'>";
	}
	else
	{
		echo "<div class='text-message'>";
		echo "<div class='post-name'>";
		echo $nickname;
		echo "</div>";
	}

	echo "<div class='post-message'>";
	echo "<div class='speech-arrow'></div>";
	echo $text;
	echo "<div class='post-time-wrapper'>";
	echo "<div class='post-time'>";
	echo $human_time;
	echo "</div>";
	echo "</div>";
	echo "</div>";


	echo "</div>";

	echo "<div class='end-of-message'>";
	echo "</div>";
}

