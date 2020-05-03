<?php

include_once("config.inc.php");
include_once("database.inc.php");
include_once("debug.inc.php");


// room ID
if (isset($_GET['room']))
{
	$room = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['room']);

	// the user that's reporting this
	if (isset($_GET['myid']))
	{
		$myid = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['myid']);
	}

	// user name of who's affected
	if (isset($_GET['name']))
	{
                $name = preg_replace($NICKNAME_REGEX, '', $_GET['name']);
	}

	// user IDs affected
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
	if (isset($_GET['allusers']))
	{
		$raw_allusers = json_decode($_GET['allusers']);
		foreach ($raw_allusers as $id => $name)
		{
			$id = preg_replace('/[^a-zA-Z0-9]/', '', $id);
			$name = preg_replace($NICKNAME_REGEX, '', $name);
			$allusers[$id] = $name;
		}
	}

	if (isset($joined) && isset($name))
	{
		userJoined($room, $joined, $name);
		if (isset($myid))
		{
			userIsActive($room, $myid);
		}
		debug("Room $room: $myid reports user $joined ($name) joined");
	}

	if (isset($left))
	{
		userLeft($room, $left);
		if (isset($myid) && $myid != $left)
		{
			userIsActive($room, $myid);
		}
		debug("Room $room: $myid reports user $left left");
	}

	if (isset($kicked))
	{
		userLeft($room, $kicked);
		if (isset($myid))
		{
			userIsActive($room, $myid);
		}
		debug("Room $room: $myid reports user $kicked kicked out");
	}

	if (isset($renamed) && isset($name))
	{
		userJoined($room, $renamed, $name);
		if (isset($myid))
		{
			userIsActive($room, $myid);
		}
		debug("Room $room: $myid reports user $renamed renamed to $name");
	}

	if (isset($allusers))
	{
		setAllUsers($room, $allusers);
		if (isset($myid))
		{
			userIsActive($room, $myid);
		}
		$numUsers = count($allusers);
		debug("Room $room: $myid reports total $numUsers users identified");
	}
}

