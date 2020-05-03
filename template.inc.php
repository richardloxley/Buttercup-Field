<?php

include_once("jitsi.inc.php");


function draw_header()
{
	global $SITE_TITLE;

	?>
		<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title><?php echo $SITE_TITLE;?></title>
			<link rel="stylesheet" href="style.css" type="text/css">
		</head>
		<body>
	<?php
}


function draw_footer()
{
	?>
		<div class="footer">
			Powered by <a href="https://github.com/richardloxley/Buttercup-Field">Buttercup Field</a> by <a href="https://www.richardloxley.com/">Richard Loxley</a>
		</div>
		</body>
		</html>
	<?php
}


function draw_nickname_form()
{
	if (edittingNickname() || getNickname() == "")
	{
		?>
			<p>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			Please choose a nickname: <input type="text" name="nickname" value="<?php echo getNickname();?>" size=50 maxlength=100>
			<input type="submit" value="Enter">
			</form>
		<?php
	}
	else
	{
		?>
			<p>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			Welcome <?php echo htmlentities(getNickname(), ENT_QUOTES);?>
			<input type="submit" name="edit_nickname" value="Change nickname">
			</form>
		<?php
	}
}


function draw_blackboard()
{
	?>
		<h2>
			Blackboard
		</h2>
	<?php

	echo "... blackboard will appear here ...";
}


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


function draw_video_chat()
{
	?>
		<h2>
			Video chat rooms
		</h2>
	<?php

	if (canDeviceShowVideo())
	{
		$rooms = get_chat_rooms();

		if (count($rooms) == 0)
		{
			echo "No rooms created yet";
		}
		else
		{
			$nickname = getNickname();
			if ($nickname == "")
			{
				echo "<p>";
				echo "Please set a nickname if you would like to join a room.";
			}


			foreach ($rooms as $room)
			{
				$roomID = $room['id'];
				$roomName = $room['name'];

				$url = "room/" . $roomID;

				if (isIOS())
				{
					$url = jitsiDeeplinkIos($roomID, $roomName, $nickname);
				}
				else if (isAndroid())
				{
					$url = jitsiDeeplinkAndroid($roomID, $roomName,$nickname);
				}

				echo "<div class='room'>";

				echo "<div class='room-name'>";
				if ($nickname != "")
				{
					echo "<a href='" . $url . "' target='_blank' rel='noopener noreferrer'>";
					echo $room['name'];
					echo "</a>";
				}
				else
				{
					echo $room['name'];
				}
				echo "</div>";

				echo "<div class='room-description'>";
				echo $room['description'];
				echo "</div>";

				echo "<div class='room-occupants'>";

				$users = getUsersIn($roomID);
				$onlyMobile = roomOnlyContainsMobileUsers($roomID);

				// don't show users if they are only mobile users and we haven't seen
				// them for more than a day - they've probably dropped off by now
				if (!$onlyMobile || !moreThanADaySince($room['last_used']))
				{
					foreach($users as $user)
					{
						if ($onlyMobile)
						{
							echo '<span class=inactive-user>';
						}
						else
						{
							echo '<span class=active-user>';
						}

						echo $user["name"];

						if ($user["mobile"])
						{
							// mobile phone unicode
							echo "&#x1F4F1";
						}

						echo '</span>';
					}

					if ($onlyMobile)
					{
						echo '<span class=last-used>';
						echo humanTimeSince($room['last_used']);
						echo '</span>';
					}
				}
				echo "</div>";

				echo "</div>";
			}
		}
	}
}
