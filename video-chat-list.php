<?php

include_once("include/password.inc.php");
include_once("include/database.inc.php");
include_once("include/jitsi.inc.php");
include_once("include/symbols.inc.php");
include_once("include/mobile.inc.php");
include_once("include/active-users.inc.php");


goHomeOnWrongPassword();



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


function moreThanADaySince($mysql_timestamp)
{
	$timestamp = strtotime($mysql_timestamp);
	$seconds = time() - $timestamp;
	return ($seconds > 60 * 60 * 24);
}


function humanTimeSince($mysql_timestamp)
{
	$timestamp = strtotime($mysql_timestamp);
	$seconds = time() - $timestamp;

	if ($seconds < 60)
	{
		// return "last updated " . pluralise($seconds, "second") . " ago";
		// don't both showing anything until it's been more than a minute
		return "";
	}

	$minutes = floor($seconds / 60);

	if ($minutes < 60)
	{
		return "last updated " . pluralise($minutes, "minute") . " ago";
	}

	$hours = floor($minutes / 60);
	return "last updated " . pluralise($hours, "hour") . " ago";
}


$rooms = get_chat_rooms();

if (count($rooms) == 0)
{
	echo "No rooms created yet";
}
else
{
	$nickname = getNickname();

	echo "<div class='room-list table'>";

	foreach ($rooms as $room)
	{
		$roomID = $room['room_id'];
		$roomName = $room['name'];

		$url = "video/" . $roomID;

		if (isIOS())
		{
			$url = jitsiDeeplinkIos($roomID, $roomName, $nickname);
		}
		else if (isAndroid())
		{
			$url = jitsiDeeplinkAndroid($roomID, $roomName,$nickname);
		}

		$start_link = "";
		$end_link = "";
		if ($nickname != "")
		{
			$start_link = "<a href='$url' target='_blank' rel='noreferrer'>";
			$end_link = "</a>";
		}

		echo "<div class='room row'>";

		echo "<div class='video-thumb cell'>";

		global $VIDEO_ROOM_THUMBNAIL_UPLOAD_DIR;
		if (file_exists("$VIDEO_ROOM_THUMBNAIL_UPLOAD_DIR/$roomID.png"))
		{
			echo $start_link;
			echo "<img src='$VIDEO_ROOM_THUMBNAIL_UPLOAD_DIR/$roomID.png'>";
			echo $end_link;
		}

		echo "</div>";

		echo "<div class='cell'>";

		echo "<div class='room-name'>";
		echo $start_link;
		echo "<span class='icons'>";
		echo icon_video_camera();
		echo "</span>";
		echo "&nbsp; ";
		echo $room['name'];
		echo $end_link;
		echo "</div>";

		echo "<div class='room-description'>";
		echo $start_link;
		echo $room['description'];
		echo $end_link;
		echo "</div>";


		$users = getUsersIn($roomID);
		$onlyMobile = roomOnlyContainsMobileUsers($roomID);

		// don't show users if they are only mobile users and we haven't seen
		// them for more than a day - they've probably dropped off by now
		if (count($users) > 0 && (!$onlyMobile || !moreThanADaySince($room['last_used'])))
		{
			echo "<div class='room-occupants'>";

			foreach ($users as $user)
			{
				if ($onlyMobile)
				{
					echo '<span class=inactive-user>';
				}
				else
				{
					echo '<span class=active-user>';
				}

				echo non_breaking_username($user["name"]);

				if ($user["mobile"])
				{
					echo symbolMobilePhone();
				}

				echo '</span>';
				echo ' ';
			}

			if ($onlyMobile)
			{
				echo '<span class=last-used>';
				echo humanTimeSince($room['last_used']);
				echo '</span>';
			}

			echo "</div>";
			echo "</div>";
		}
		else
		{
			echo "</div>";

			// room is empty, so we can offer the option to delete it
			if ($nickname != "")
			{
				?>
					<div class="cell delete-a-room">
						<form method="post" action="/" onsubmit="return confirm('If you delete this room it will be permanently deleted for all users of the site.  Are you sure you want to delete this room?');">
							<input type="hidden" name="delete_room_id" value="<?php echo $roomID;?>">
							<input type="submit" name='delete_room' class='icons' value="<?php echo icon_trash();?>">
						</form>
					</div>
				<?php
			}
		}

		echo "</div>";
	}

	echo "</div>";
}
