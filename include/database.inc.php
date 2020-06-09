<?php

include_once(__DIR__ . "/../config.inc.php");
include_once(__DIR__ . "/debug.inc.php");


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
		mysqli_close($conn);
		return $result;
	}
	else
	{
		error_log("MySQL error: " . mysqli_error($conn) . " while executing: " . $sql);
		mysqli_close($conn);
		return null;
	}
}


function execute_sql_multiple($sql)
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

	if ($result = mysqli_multi_query($conn, $sql))
	{
		mysqli_close($conn);
		return $result;
	}
	else
	{
		error_log("MySQL error: " . mysqli_error($conn) . " while executing: " . $sql);
		mysqli_close($conn);
		return null;
	}
}


function post_text_chat($chat_room_id, $nickname, $message)
{
	$user_id = get_user_id($nickname);

	if ($user_id < 0)
	{
		return;
	}

	$sql = "insert into text_chat (chat_room_id, user_id, message) values ('$chat_room_id', '$user_id', '$message')";
	execute_sql($sql);
}


function get_text_chats($chat_room_id, $since_message_id)
{
	// first clear out old messages

	global $TEXT_CHAT_RETENTION_IN_HOURS;

	$sql = "delete from text_chat where posted < date_sub(now(), interval 12 hour)";
	execute_sql($sql);


	// now get any new messages

	$messages = array();

	$sql = "select message_id, posted, message, nickname from text_chat inner join users using (user_id) where chat_room_id = $chat_room_id and message_id > $since_message_id order by message_id asc";

	$result = execute_sql($sql);

	if ($result)
	{
		while ($row = mysqli_fetch_array($result))
		{
			$messages[] = $row;
		}

		mysqli_free_result($result);
	}

	return $messages;
}


function get_chat_rooms()
{
	$rooms = array();

	$sql = "select video_rooms.*, count(jitsi_user_id) as active_users from video_rooms left join video_users using(room_id) group by video_rooms.room_id order by active_users desc, last_used desc, created asc";

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


function getRoomNameFor($roomID)
{
	$sql = "select name from video_rooms where room_id = '$roomID'";
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
	$sql = "select jitsi_name, has_javascript from video_users where room_id = '$roomID' order by first_seen";
	$result = execute_sql($sql);

	$names = array();

	if ($result)
	{
		while ($row = mysqli_fetch_array($result))
		{
			$names[] = [ "name" => $row['jitsi_name'], "mobile" => !$row['has_javascript'] ];
		}
		mysqli_free_result($result);
	}

	return $names;
}


function roomOnlyContainsMobileUsers($roomID)
{
	$sql = "select sum(has_javascript) as javascript, count(*) as total from video_users where room_id = '$roomID'";
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
	$sql = "update video_rooms set last_used = current_timestamp() where room_id = '$roomID'";
	execute_sql($sql);
}


function userJoined($roomID, $jitsiUserID, $jitsiName)
{
	$sql = "insert into video_users (room_id, jitsi_user_id, jitsi_name, first_seen, last_seen, has_javascript) values ('$roomID', '$jitsiUserID', '$jitsiName', current_timestamp(), current_timestamp(), false) on duplicate key update jitsi_name = '$jitsiName', last_seen = current_timestamp()";
	execute_sql($sql);

	updateRoom($roomID);
}


function userLeft($roomID, $jitsiUserID)
{
	$sql = "delete from video_users where room_id = '$roomID' and jitsi_user_id = '$jitsiUserID'";
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
	$sql = "delete from video_users where room_id = '$roomID' and jitsi_user_id not in ($all_ids)";
	execute_sql($sql);

	updateRoom($roomID);
}


function userIsActiveInRoom($roomID, $jitsiUserID)
{
	$sql = "update video_users set has_javascript = true, last_seen = current_timestamp() where room_id = '$roomID' and jitsi_user_id = '$jitsiUserID'";
	execute_sql($sql);

	updateRoom($roomID);
}


function get_user($nickname)
{
	$sql = "select * from users where nickname = '$nickname'";

	$result = execute_sql($sql);

	$user = null;

	if ($result)
	{
		$user = mysqli_fetch_array($result);
		mysqli_free_result($result);
	}

	return $user;
}


function get_user_id($nickname)
{
	$user = get_user($nickname);

	if ($user == null)
	{
		return -1;
	}
	else
	{
		return $user["user_id"];
	}
}


function create_user($nickname)
{
	$sql = "insert into users (nickname) values ('$nickname')";
	execute_sql($sql);
}


function user_is_active($nickname)
{
	$sql = "update users set last_active = current_timestamp() where nickname = '$nickname'";
	execute_sql($sql);
}


function get_users_active()
{
	// active on site in last minute, also note if they've posted a text chat in the last 5 minutes
	$sql = "select nickname, (not posted is null) as active_in_chat from users left join (select * from text_chat where (posted > date_sub(now(), interval 5 minute))) as active_texts using (user_id) where (last_active > date_sub(now(), interval 1 minute)) group by nickname";

	$result = execute_sql($sql);

	$users = array();

	if ($result)
	{
		while ($row = mysqli_fetch_array($result))
		{
			$name = $row['nickname'];
			$users[$name] = [ "chatting" => ($row['active_in_chat'] == 1), "video" => false, "mobile" => false ];
		}
		mysqli_free_result($result);
	}

	// also find active video users (because Jitsi names can be changed, the nicknames may not match)
	// we also cannot be sure if a user is truly active if only mobile users are left in a room as
	// they don't have javascript to report back when they leave, so we'll omit any rooms with only mobile users
	$sql = "select jitsi_name as name, (not has_javascript) as mobile from (select room_id from (select room_id, sum(has_javascript) as js_count from video_users group by room_id) as rooms where js_count > 0) as rooms_we_can_see_into join video_users using (room_id)";

	$result = execute_sql($sql);

	if ($result)
	{
		while ($row = mysqli_fetch_array($result))
		{
			$name = $row['name'];
			if (isset($users[$name]))
			{
				$users[$name]["video"] = true;
				$users[$name]["mobile"] = $row["mobile"];	
			}
			else
			{
				$users[$name] = [ "chatting" => false, "video" => true, "mobile" => $row["mobile"] ];
			}
		}
		mysqli_free_result($result);
	}

	ksort($users, SORT_NATURAL | SORT_FLAG_CASE);

	return $users;
}


function get_users_active_earlier()
{
	// active after midnight but more than a minute ago
	$sql = "select nickname from users where last_active < date_sub(now(), interval 1 minute) and last_active > timestamp(current_date) order by nickname asc";

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


function new_blackboard($nickname)
{
	$sql = "select max(board_id) as max_board_id from blackboards";
	$result = execute_sql($sql);

	$new_board_id = 1;

	if ($result)
	{
		$row = mysqli_fetch_array($result);
		$max_board_id = $row['max_board_id'];
		if ($max_board_id != null)
		{
			$new_board_id = $max_board_id + 1;
		}
		mysqli_free_result($result);
	}

	append_to_blackboard($new_board_id, $nickname, "");

	return $new_board_id;
}


function delete_blackboard($board_id)
{
	$sql = "delete from blackboards where board_id = '$board_id'";
	execute_sql($sql);
}


function get_blackboard_ids()
{
	$sql = "select board_id from blackboards group by board_id order by board_id asc";
	$result = execute_sql($sql);

	$boards = array();

	while ($row = mysqli_fetch_array($result))
	{
		$boards[] = $row["board_id"];
	}

	mysqli_free_result($result);

	return $boards;
}


function get_blackboard_contents($board_id)
{
	$sql = "select nickname, message_id, text, (sub_position > 0) as is_reply, edit_timestamp, (edit_timestamp > date_sub(now(), interval 12 hour)) as recent_12, (edit_timestamp > date_sub(now(), interval 24 hour)) as recent_24 from blackboards inner join users using (user_id) where board_id = '$board_id' order by position asc, sub_position asc";

	$result = execute_sql($sql);

	$entries = array();

	while ($row = mysqli_fetch_array($result))
	{
		$entries[] =
		[
			"message_id" => $row["message_id"],
			"text" => $row["text"],
			"is_reply" => $row["is_reply"],
			"nickname" => $row["nickname"],
			"recent_12" => $row["recent_12"],
			"recent_24" => $row["recent_24"],
			"edit_timestamp" => $row["edit_timestamp"]
		];
	}

	mysqli_free_result($result);

	return $entries;
}


function append_to_blackboard($board_id, $nickname, $text)
{
	$user_id = get_user_id($nickname);

	if ($user_id < 0)
	{
		return;
	}

	$position_sql = "select max(position) as max_position from blackboards where board_id = '$board_id'";
	$result = execute_sql($position_sql);

	$new_position = 0;

	if ($result)
	{
		$row = mysqli_fetch_array($result);
		$max_position = $row['max_position'];
		if ($max_position != null)
		{
			$new_position = $max_position + 1;
		}
		mysqli_free_result($result);
	}

	$sql = "insert into blackboards (board_id, user_id, text, position) values ('$board_id', '$user_id', '$text', '$new_position')";
	$result = execute_sql($sql);
}


function blackboard_edit($message_id, $nickname, $text)
{
	$user_id = get_user_id($nickname);

	if ($user_id < 0)
	{
		return;
	}

	$sql = "update blackboards set user_id = $user_id, text = '$text', edit_timestamp = current_timestamp() where message_id = $message_id";
	execute_sql($sql);
}


function blackboard_reply($board_id, $message_id, $nickname, $text)
{
}

function delete_from_blackboard($message_id)
{
// only works if deleting a top-level item
	$sql = "lock table blackboards write;";
	$sql .= "select @board := board_id, @pos := position from blackboards where message_id = '$message_id';";
	// delete anything with same position number to delete replies too
	$sql .= "delete from blackboards where board_id = @board and position = @pos;";
	$sql .= "update blackboards set position = position - 1 where board_id = @board and position > @pos;";
	$sql .= "unlock tables;";
	execute_sql_multiple($sql);
}

function blackboard_move_up($message_id)
{
// should check top/bottom
	$sql = "lock table blackboards write;";
	$sql .= "select @board := board_id, @pos := position from blackboards where message_id = '$message_id';";
	$sql .= "update blackboards set position = -1 where board_id = @board and position = @pos;";
	$sql .= "update blackboards set position = @pos where board_id = @board and position = @pos - 1;";
	$sql .= "update blackboards set position = @pos - 1 where board_id = @board and position = -1;";
	$sql .= "unlock tables;";
	execute_sql_multiple($sql);
}

function blackboard_move_down($message_id)
{
// should check top/bottom
	$sql = "lock table blackboards write;";
	$sql .= "select @board := board_id, @pos := position from blackboards where message_id = '$message_id';";
	$sql .= "update blackboards set position = -1 where board_id = @board and position = @pos;";
	$sql .= "update blackboards set position = @pos where board_id = @board and position = @pos + 1;";
	$sql .= "update blackboards set position = @pos + 1 where board_id = @board and position = -1;";
	$sql .= "unlock tables;";
	execute_sql_multiple($sql);
}


function new_video_room($nickname, $room_id, $room_name, $room_description)
{
	$user_id = get_user_id($nickname);

	if ($user_id < 0)
	{
		return;
	}

	$sql = "insert into video_rooms (user_id, room_id, name, description) values ('$user_id', '$room_id', '$room_name', '$room_description')";
	execute_sql($sql);
}


function delete_video_room($room_id)
{
	$sql = "delete from video_rooms where room_id = '$room_id'";
	execute_sql($sql);
}
