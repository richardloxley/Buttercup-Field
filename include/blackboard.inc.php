<?php

include_once(__DIR__ . "/symbols.inc.php");


function draw_blackboard_thumbs()
{
	echo "<div id='blackboard-home'>";

	$ids = get_blackboard_ids();

	foreach($ids as $id)
	{
		draw_blackboard_thumb($id);
	}

	draw_blackboard_thumb_end();

	echo "</div>";
}


function draw_blackboard_thumb($boardID)
{
	$messages = get_blackboard_contents($boardID);

	$title = array_shift($messages)["text"];
	if ($title == "")
	{
		$title = "Empty board";
	}

	echo "<a href='/blackboard/$boardID'>";
	echo "<div class='blackboard blackboard-thumb'>";

	echo "<table>";

	echo "<tr>";
	echo "<td class='chalkTitle'>";
	echo $title;
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td class='chalkTitle'>";
	echo "&nbsp;";
	echo "</td>";
	echo "</tr>";

	$num_fonts = 7;

	foreach ($messages as $message)
	{
		$who = $message["nickname"];
		$when = $message["edit_timestamp"];
		$recent_12 = $message["recent_12"];
		$recent_24 = $message["recent_24"];
		$is_reply = $message["is_reply"];
		$message_id = $message["message_id"];
		$text = $message["text"];

		$recent_tag = "";

		if ($recent_12)
		{
			$recent_tag = "recent_12";
		}
		else if ($recent_24)
		{
			$recent_tag = "recent_24";
		}

		$font = crc32($who) % $num_fonts;

		echo "<tr>";
		echo "<td class='chalk$font blackboard-ellipsis $recent_tag'>";
		echo $text;
		echo "</td>";
		echo "</tr>";
	}

	echo "</table>";

	echo "<div class='zoom icons'>";
	echo icon_zoom();
	echo "</div>";

	echo "</div>";
	echo "</a>";
}

function draw_blackboard_thumb_end()
{
	?>
		<div class='blackboard-thumb-end'>
		</div>
	<?php

	if (getNickname() != "")
	{
		?>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<input type="submit" name="new_blackboard" value="Add a new blackboard">
			</form>
		<?php
	}
}
