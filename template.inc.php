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
				foreach($users as $user)
				{
					echo '<span class=active-user>';
					echo $user;
					echo '</span>';
				}
				echo "</div>";

				echo "</div>";
			}
		}
	}
}
