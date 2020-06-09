<?php

include_once("config.inc.php");
include_once("include/database.inc.php");
include_once("include/debug.inc.php");
include_once("include/user-input.inc.php");
include_once("include/password.inc.php");

goHomeOnWrongPassword();


// room ID
if (is_variable_set('room'))
{
	$room = sanitised_as_alphanumeric('room');

	// the user that's reporting this
	if (is_variable_set('myid'))
	{
		$myid = sanitised_as_alphanumeric('myid');
	}

	// user name of who's affected
	if (is_variable_set('name'))
	{
                $name = sanitised_as_alphanumeric_extended('name');
	}

	// user IDs affected
	if (is_variable_set('joined'))
	{
		$joined = sanitised_as_alphanumeric('joined');
	}
	if (is_variable_set('left'))
	{
		$left = sanitised_as_alphanumeric('left');
	}
	if (is_variable_set('kicked'))
	{
		$kicked = sanitised_as_alphanumeric('kicked');
	}
	if (is_variable_set('renamed'))
	{
		$renamed = sanitised_as_alphanumeric('renamed');
	}
	if (is_variable_set('allusers'))
	{
		$unsafe_allusers = json_decode(get_unsafe_variable('allusers'));
		foreach ($unsafe_allusers as $unsafe_id => $unsafe_name)
		{
			$id = to_alphanumeric($unsafe_id);
			$name = to_alphanumeric_extended($unsafe_name);
			$allusers[$id] = $name;
		}
	}

	if (isset($joined) && isset($name))
	{
		userJoined($room, $joined, $name);
		if (isset($myid))
		{
			userIsActiveInRoom($room, $myid);
		}
		debug("Room $room: $myid reports user $joined ($name) joined");
	}

	if (isset($left))
	{
		userLeft($room, $left);
		if (isset($myid) && $myid != $left)
		{
			userIsActiveInRoom($room, $myid);
		}
		debug("Room $room: $myid reports user $left left");
	}

	if (isset($kicked))
	{
		userLeft($room, $kicked);
		if (isset($myid))
		{
			userIsActiveInRoom($room, $myid);
		}
		debug("Room $room: $myid reports user $kicked kicked out");
	}

	if (isset($renamed) && isset($name))
	{
		userJoined($room, $renamed, $name);
		if (isset($myid))
		{
			userIsActiveInRoom($room, $myid);
		}
		debug("Room $room: $myid reports user $renamed renamed to $name");
	}

	if (isset($allusers))
	{
		setAllUsers($room, $allusers);
		if (isset($myid))
		{
			userIsActiveInRoom($room, $myid);
		}
		$numUsers = count($allusers);
		debug("Room $room: $myid reports total $numUsers users identified");
	}
}

