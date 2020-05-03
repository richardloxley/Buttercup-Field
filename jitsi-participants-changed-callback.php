<?php

include_once("config.inc.php");
include_once("database.inc.php");
include_once("debug.inc.php");


// room ID
if (isset($_GET['room']))
{
	$room = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['room']);

	// user name
	if (isset($_GET['name']))
	{
                $name = preg_replace($NICKNAME_REGEX, '', $_GET['name']);
	}

	// user IDs
	if (isset($_GET['joined']))
	{
		$joined = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['joined']);
	}
	if (isset($_GET['left']))
	{
		$left = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['left']);
	}
	if (isset($_GET['kicked']))
	{
		$kicked = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['kicked']);
	}
	if (isset($_GET['renamed']))
	{
		$renamed = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['renamed']);
	}

	if (isset($joined) && isset($name))
	{
		userJoined($room, $joined, $name);
		debug("Room $room: user $joined ($name) joined");
	}

	if (isset($left))
	{
		userLeft($room, $left);
		debug("Room $room: user $left left");
	}

	if (isset($kicked))
	{
		userLeft($room, $kicked);
		debug("Room $room: user $kicked kicked out");
	}

	if (isset($renamed) && isset($name))
	{
		userJoined($room, $renamed, $name);
		debug("Room $room: user $renamed renamed to $name");
	}
}

