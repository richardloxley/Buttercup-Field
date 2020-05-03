<?php

include_once("config.inc.php");
include_once("debug.inc.php");


function execute_sql($sql)
{
	// debug($sql);

	global $DB_SERVERNAME;
	global $DB_USERNAME;
	global $DB_PASSWORD;
	global $DB_DATABASE;

	$conn = mysqli_connect($DB_SERVERNAME, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);

	if (!$conn)
	{
		error_log("MySQL connection failed: " . mysqli_connect_error());
		return null;
	}

	if ($result = mysqli_query($conn, $sql))
	{
		return $result;
	}
	else
	{
		error_log("MySQL error: " . mysqli_error($conn) . " while executing: " . $sql);
		return null;
	}
}


function get_chat_rooms()
{
	$rooms = array();

	$sql = "select rooms.*, count(user_id) as active_users from rooms left join active_users on rooms.id = active_users.room_id group by rooms.id order by active_users desc, last_used desc, created asc";

	$result = execute_sql($sql);

	if ($result)
	{
		while ($row = mysqli_fetch_array($result))
		{
			$rooms[] = $row;
		}

		mysqli_free_result($result);
	}

	return $rooms;
}


function sanitiseRoomID($room)
{
	return preg_replace("/[^a-zA-Z0-9]/", "", $room);
}


function getRoomNameFor($roomID)
{
	$sanitised = sanitiseRoomID($roomID);

	$sql = "select name from rooms where id = '" . $sanitised . "'";

	$result = execute_sql($sql);

	$name = "";

	if ($result)
	{
		$row = mysqli_fetch_array($result);
		$name = $row['name'];
		mysqli_free_result($result);
	}

	return $name;
}


function getUsersIn($roomID)
{
	$sanitised = sanitiseRoomID($roomID);

	$sql = "select nickname, has_javascript from active_users where room_id = '$sanitised' order by first_seen";

	$result = execute_sql($sql);

	$names = array();

	if ($result)
	{
		while ($row = mysqli_fetch_array($result))
		{
			$names[] = [ "name" => $row['nickname'], "mobile" => !$row['has_javascript'] ];
		}
		mysqli_free_result($result);
	}

	return $names;
}


function roomOnlyContainsMobileUsers($roomID)
{
	$sanitised = sanitiseRoomID($roomID);

	$sql = "select sum(has_javascript) as javascript, count(*) as total from active_users where room_id = '$roomID'";

	$result = execute_sql($sql);

	if ($result)
	{
		$row = mysqli_fetch_array($result);
		$total = $row['total'];
		$javascript = $row['javascript'];
		mysqli_free_result($result);

		if ($total > 0 && $javascript == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}



function updateRoom($roomID)
{
	$sql = "update rooms set last_used = current_timestamp() where id = '$roomID'";
	execute_sql($sql);
}


function userJoined($roomID, $userID, $userName)
{
	$sql = "insert into active_users (room_id, user_id, nickname, first_seen, last_seen, has_javascript) values ('$roomID', '$userID', '$userName', current_timestamp(), current_timestamp(), false) on duplicate key update nickname = '$userName', last_seen = current_timestamp()";
	execute_sql($sql);

	updateRoom($roomID);
}


function userLeft($roomID, $userID)
{
	$sql = "delete from active_users where room_id = '$roomID' and user_id = '$userID'";
	execute_sql($sql);

	updateRoom($roomID);
}


function setAllUsers($roomID, $allusers)
{
	$all_ids = "";

	// add/update each user found
	foreach ($allusers as $id => $name)
	{
		userJoined($roomID, $id, $name);
		if ($all_ids != "")
		{
			$all_ids .= ", ";
		}
		$all_ids .= "'$id'";
	}

	// delete any that weren't in that list
	$sql = "delete from active_users where room_id = '$roomID' and user_id not in ($all_ids)";
	execute_sql($sql);

	updateRoom($roomID);
}

function userIsActive($roomID, $userID)
{
	$sql = "update active_users set has_javascript = true, last_seen = current_timestamp() where room_id = '$roomID' and user_id = '$userID'";
	execute_sql($sql);

	updateRoom($roomID);
}
