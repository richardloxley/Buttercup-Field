<?php

include_once("symbols.inc.php");


function draw_blackboard_thumbs()
{
	$ids = get_blackboard_ids();

	foreach($ids as $id)
	{
		draw_blackboard_thumb($id);
	}

	draw_blackboard_thumb_end();
}


function draw_blackboard_thumb($boardID)
{
	$messages = get_blackboard_contents($boardID);

	$title = array_shift($messages)["text"];
	if ($title == "")
	{
		$title = "Empty board";
	}

	echo "<a href='blackboard/$boardID'>";
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

	$num_fonts = 8;

	foreach ($messages as $message)
	{
		$who = $message["nickname"];
		$when = $message["edit_timestamp"];
		$is_reply = $message["is_reply"];
		$message_id = $message["message_id"];
		$text = htmlspecialchars($message["text"], ENT_QUOTES);

		$font = crc32($who) % $num_fonts;

		echo "<tr>";
		echo "<td class='chalk$font blackboard-ellipsis'>";
		echo $text;
		echo "</td>";
		echo "</tr>";
	}

	echo "</table>";

	echo "<div class='zoom'>";
	echo symbolMagnifyingGlass();
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
