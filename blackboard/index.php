<?php
        include_once("../user-input.inc.php");
        include_once("../database.inc.php");
        include_once("../config.inc.php");
        include_once("../blackboard.inc.php");

	$userNickname = getNickname();

	$editable = false;

	if ($userNickname != "")
	{
		$editable = true;
	}

	$boardID = -1;

	if (is_variable_set("board"))
	{
		$boardID = sanitised_as_int("board");
	}

	if (is_variable_set("finish-add-new") && is_variable_set("blackboard-add-new"))
	{
		$text = sanitised_as_text("blackboard-add-new");
		append_to_blackboard($boardID, $userNickname, $text);
	}

/*
	need to write DB end:
		finish-edit
			function blackboard_edit($board_id, $message_id, $nickname, $text)
		delete
			function delete_from_blackboard($board_id, $message_id)
		up
			function blackboard_move_up($board_id, $message_id)
		down
			function blackboard_move_down($board_id, $message_id)
		finish-reply
			function blackboard_reply($board_id, $message_id, $nickname, $text)
*/

	$messages = get_blackboard_contents($boardID);

	$title = array_shift($messages)["text"];
	$page_title = $title;

	if ($title == "")
	{
		$page_title = "Empty board";
	}


?>
	<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $page_title;?></title>
		<link rel="stylesheet" href="../style.css" type="text/css">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
	</head>
	<body class="blackboard-body">

	<script type="text/javascript">

	$(document).ready(function()
	{
		$('.edit').click(function()
		{
			// replace entry with edit box
			$(this.parentNode.parentNode.parentNode).find("[class=blackboard-item]").hide();	
			$(this.parentNode.parentNode.parentNode).find("[class=blackboard-item-edit]").show();	
			// stop mouse-over hover on all the other rows
			$(".buttons").addClass("buttons-editing");
			// show the enter button
			$(this.parentNode.parentNode).find("[class=buttons-enter]").show();	
			// hide the other buttons (they are invisble but take up space)
			$(this.parentNode).hide();	
			// hide the Add New button
			$(".add-new").hide();
			// don't submit form
			return false;
		});

		$('.delete').click(function()
		{
			// check they want to submit form
			return confirm('Are you sure you want to delete this?');
		});

		$('.reply').click(function()
		{
			// show the text entry box
			$(this).closest("tr").next().show();
			// stop all mouse-over hover
			$(".buttons").addClass("buttons-editing");
			// don't submit form
			return false;
		});


		$('.add-new').click(function()
		{
			// hide the Add New button
			$(".add-new").hide();
			// show the text entry box
			$(".add-new-edit").show();
			// show the enter button
			$(".add-new-enter").show();
			// stop all mouse-over hover
			$(".buttons").addClass("buttons-editing");
			// don't submit form
			return false;
		});
	});

	</script>
<?php
	if ($boardID < 0)
	{
		echo "<div class='blackboard blackboard-full'>";
		echo "<table>";
		echo "<tr>";
		echo "<td class='chalkTitle'>";
		echo "This board doesn't exist";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
	}
	else
	{
		echo "<div class='blackboard blackboard-full'>";

		$postURL = $_SERVER["REQUEST_URI"];
		echo "<form method='post' action='$postURL'>";

		echo "<table>";

		echo "<tr>";

		echo "<td class='chalkTitle'>";

		echo "<div class='blackboard-item'>";
		if ($title == "")
		{
			echo "Click the edit button to the right of this text to add a title";
		}
		else
		{
			echo $title;
		}
		echo "</div>";

		if ($editable)
		{
			$message_id = $messages[0]["message_id"];
			// edit text box (usually hidden)
			echo "<div class='blackboard-item-edit'>";
			echo "<input type='hidden' name='blackboard-item-id' value='$message_id'>";
			echo "<textarea name='blackboard-item-edit' class='chalkTitle' rows=2 maxlength=200>";
			echo $title;
			echo "</textarea>";
			echo "</div>";
		}

		echo "</td>";

		if ($editable)
		{
			echo "<td>";

			// buttons (visible on hover)
			echo "<div class='buttons'>";
			echo "<input type='submit' class='edit' name='edit' value='Edit'>";
			echo "</div>";

			// enter button (usually hidden)
			echo "<div class='buttons-enter'>";
			echo "<input type='submit' class='finish-edit' name='finish-edit' value='Enter'>";
			echo "</div>";

			echo "</td>";
		}

		echo "</tr>";

		echo "<tr>";
		echo "<td class='chalkTitle'>";
		echo "&nbsp;";
		echo "</td>";
		echo "</tr>";

		$num_fonts = 8;

		$my_font = crc32($userNickname) % $num_fonts;

		foreach ($messages as $message)
		{
			$who = $message["nickname"];
			$when = $message["edit_timestamp"];
			$is_reply = $message["is_reply"];
			$message_id = $message["message_id"];
			$text = htmlspecialchars($message["text"], ENT_QUOTES);

			$font = crc32($who) % $num_fonts;

			echo "<tr>";

			echo "<td class='chalk$font'>";

			// current item
			echo "<div class='blackboard-item'>";
			echo $text;
			echo "</div>";

			if ($editable)
			{
				// edit text box (usually hidden)
				echo "<div class='blackboard-item-edit'>";
				echo "<input type='hidden' name='blackboard-item-id' value='$message_id'>";
				echo "<textarea name='blackboard-item-edit' class='chalk$my_font' rows=2 maxlength=200>";
				echo $text;
				echo "</textarea>";
				echo "</div>";
			}

			echo "</td>";

			if ($editable)
			{
				echo "<td>";

				// buttons (visible on hover)
				echo "<div class='buttons'>";
				echo "<input type='submit' class='reply'  name='reply'  value='Reply'>";
				echo "<input type='submit' class='edit'   name='edit'   value='Edit'>";
				echo "<input type='submit' class='delete' name='delete' value='Delete'>";
				echo "<input type='submit'                name='up'     value='Up'>";
				echo "<input type='submit'                name='down'   value='Down'>";
				echo "</div>";

				// enter button (usually hidden)
				echo "<div class='buttons-enter'>";
				echo "<input type='submit' class='finish-edit' name='finish-edit' value='Enter'>";
				echo "</div>";

				echo "</td>";
			}

			echo "</tr>";

			if ($editable)
			{
				// reply box (usually hidden)
				echo "<tr class='blackboard-item-reply'>";
				echo "<td class='chalk$font'>";
				echo "<input type='hidden' name='blackboard-item-id' value='$message_id'>";
				echo "<textarea name='blackboard-item-reply' class='chalk$my_font' rows=2 maxlength=200>";
				echo "</textarea>";
				echo "</td>";

				echo "<td>";
				echo "<input type='submit' class='finish-reply' name='finish-reply' value='Enter'>";
				echo "</td>";

				echo "</tr>";
			}
		}

		if ($editable)
		{
			// spacer
			echo "<tr>";
			echo "<td class='add-new'>";
			echo "&nbsp;";
			echo "</td>";
			echo "</tr>";

			// "add new" button
			echo "<tr>";
			echo "<td class='add-new'>";
			echo "<input type='submit' name='new' value='Add a new entry'>";
			echo "</td>";
			echo "</tr>";

			// text area (usually hidden)
			echo "<tr class='add-new-edit'>";

			echo "<td>";
			echo "<textarea name='blackboard-add-new' class='chalk$my_font' rows=2 maxlength=200>";
			echo "</textarea>";
			echo "</td>";

			// enter button (usually hidden)
			echo "<td>";

			echo "<div class='add-new-enter'>";
			echo "<input type='submit' class='finish-add-new' name='finish-add-new' value='Enter'>";
			echo "</div>";

			echo "</td>";

			echo "</tr>";
		}

		echo "</table>";

		echo "</form>";

		echo "</div>";
	}
?>

</body>
</html>

