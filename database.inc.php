<?php

include_once("config.inc.php");


function execute_sql($sql)
{
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

	$sql = "select * from rooms order by timestamp asc";

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

	$sql = "select nickname from active_users where room_id = '$sanitised' order by last_seen";

	$result = execute_sql($sql);

	$names = array();

	if ($result)
	{
		while ($row = mysqli_fetch_array($result))
		{
			$names[] = $row['nickname'];
		}
		mysqli_free_result($result);
	}

	return $names;
}



function userJoined($roomID, $userID, $userName)
{
	$sql = "replace into active_users (room_id, user_id, nickname, last_seen) values ('$roomID', '$userID', '$userName', current_timestamp())";
	execute_sql($sql);
}


function userLeft($roomID, $userID)
{
	$sql = "delete from active_users where room_id = '$roomID' and user_id = '$userID'";
	execute_sql($sql);
}
