<?php
        include_once("../config.inc.php");
        include_once("../include/user-input.inc.php");
        include_once("../include/database.inc.php");
        include_once("../include/blackboard.inc.php");
	include_once("../include/password.inc.php");
	include_once("../include/template.inc.php");

	goHomeOnWrongPassword();

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
		

	if ($editable && $boardID >= 0)
	{
		if (is_variable_set("finish-add-new") && is_variable_set("blackboard-add-new"))
		{
			$text = sanitised_as_text("blackboard-add-new");
			if ($text != "")
			{
				append_to_blackboard($boardID, $userNickname, $text);
			}
		}

		if (is_variable_set("finish-edit") && is_variable_set("blackboard-item-id") && is_variable_set("blackboard-item-edit"))
		{
			$message_id = sanitised_as_int("blackboard-item-id");
			$text = sanitised_as_text("blackboard-item-edit");
			if ($text != "")
			{
				blackboard_edit($message_id, $userNickname, $text);
			}
		}

		if (is_variable_set("delete") && is_variable_set("blackboard-item-id"))
		{
			$message_id = sanitised_as_int("blackboard-item-id");
			delete_from_blackboard($message_id);
		}

// todo move on replies

		if (is_variable_set("up") && is_variable_set("blackboard-item-id"))
		{
			$message_id = sanitised_as_int("blackboard-item-id");
			blackboard_move_up($message_id);
		}

		if (is_variable_set("down") && is_variable_set("blackboard-item-id"))
		{
			$message_id = sanitised_as_int("blackboard-item-id");
			blackboard_move_down($message_id);
		}
	/*
			finish-reply
				function blackboard_reply($board_id, $message_id, $nickname, $text)
	*/
	}

	$messages = get_blackboard_contents($boardID);

	if (count($messages) == 0)
	{
		$title = "This board doesn't exist";
		$page_title = "Board not found";
		$editable = false;
	}
	else
	{
		$title_object = array_shift($messages);
		$title = $title_object["text"];
		$title_id = $title_object["message_id"];
		$page_title = $title;

		if ($title == "")
		{
			$page_title = "Unnamed board";
		}
	}

	draw_header($page_title, "blackboard-body");
?>
	<script type="text/javascript">

	function showEditBox(row)
	{
		// replace entry with edit box
		row.find(".blackboard-item-edit").show();	
		row.find(".blackboard-item").hide();	
		// put the cursor in the text box
		var textbox = row.find("textarea");
		textbox.focus();
		// move cursor to the end (by deleting and reinserting the text)
		var text = textbox.val();
		textbox.val("");
		textbox.val(text);
		// show the enter button
		row.find(".buttons-enter").show();	
		// hide the other buttons (they are invisble but take up space)
		row.find(".buttons").hide();	
		// hide the Add New button
		$(".add-new").hide();
		// stop mouse-over hover on all the other rows
		$(".buttons").addClass("buttons-editing");
	}

	$(document).ready(function()
	{
		$('.edit').click(function()
		{
			showEditBox($(this).closest(".row"));
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
			$(this).closest(".row").next().css("display", "table-row");
			// put the cursor in the text box
			$(this).closest(".row").next().find("textarea").focus();	
			// stop all mouse-over hover
			$(".buttons").addClass("buttons-editing");
			// don't submit form
			return false;
		});

		$('.add-new').click(function()
		{
			// hide the Add New button
			$(".add-new").hide();
			// show the text entry box and enter button
			$(".add-new-edit").css("display", "table-row");
			// put the cursor in the text box
			$(".add-new-edit").find("textarea").focus();	
			// stop all mouse-over hover
			$(".buttons").addClass("buttons-editing");
			// don't submit form
			return false;
		});

		$("textarea").keydown(function(e)
		{
			// enter submits a textarea
			if (e.which == 13)
			{
				$(this).closest("form").find(".default-button").click();
				e.preventDefault();
			}

			// escape cancels a textarea
			if (e.which == 27)
			{
				$(this).closest("form").find(".cancel-button").click();
				e.preventDefault();
			}
		});

<?php
		if ($title == "")
		{
			// no title so get JS to prompt them to enter one
?>
			showEditBox($(".blackboard-title"));
<?php
		}
?>
	});

	</script>
<?php
	draw_touchscreen_detection();

	$postURL = $_SERVER["REQUEST_URI"];

	echo "<div class='blackboard blackboard-full'>";
	echo "<div class='table'>";

	// top buttons
	echo "<div class='row'>";

	// back button
	echo "<span class='cell'>";
	echo "<form method='post' action='/'>";
	echo "<input type='submit' name='back' class='icons' value='" . icon_back_hand() . "'>";
	echo "</form>";
	echo "</span>";

	// delete board button
	if ($editable && count($messages) == 0)
	{
		// only allow them to delete the board when it's empty
		echo "<span class='cell buttons-cell'>";
		echo "<form method='post' action='/'>";
		echo "<input type='hidden' name='board-id' value='$boardID'>";
		echo "<input type='submit' class='delete' name='delete-blackboard' value='Delete this board'>";
		echo "</form>";
		echo "</span>";
	}

	echo "</div>";


	// title
	echo "<form class='row blackboard-title highlightable' method='post' action='$postURL'>";
	echo "<input type='hidden' name='blackboard-item-id' value='$title_id'>";

	echo "<span class='cell chalkTitle'>";

	echo "<div class='blackboard-item'>";
	if ($title == "")
	{
		echo "Unnamed board";
	}
	else
	{
		echo $title;
	}
	echo "</div>";

	if ($editable)
	{
		// edit text box (usually hidden)
		echo "<div class='blackboard-item-edit'>";
		echo "<textarea name='blackboard-item-edit' class='chalkTitle' rows=2 maxlength=200 placeholder='Please enter a title for this board'>";
		echo $title;
		echo "</textarea>";
		echo "</div>";
	}

	echo "</span>";

	if ($editable)
	{
		echo "<span class='cell buttons-cell'>";

		// buttons (visible on hover)
		echo "<div class='buttons'>";
		echo "<input type='submit' class='edit icons' name='edit' value='" . icon_edit() . "'>";
		echo "</div>";

		// enter/cancel buttons (usually hidden)
		echo "<div class='buttons-enter'>";
		echo "<input type='submit' class='icons finish-edit default-button' name='finish-edit' value='" . icon_tick() . "'>";
		echo "<input type='submit' class='icons finish-edit cancel-button' name='cancel-edit' value='" . icon_cross() . "'>";
		echo "</div>";

		echo "</span>";
	}

	echo "</form>";


	// messages

	$num_fonts = 7;
	$my_font = crc32($userNickname) % $num_fonts;

	foreach ($messages as $index => $message)
	{
		$up_disabled = "";
		$down_disabled = "";

		if ($index == 0)
		{
			$up_disabled = "disabled";
		}

		if ($index == count($messages) - 1)
		{
			$down_disabled = "disabled";
		}

		$who = $message["nickname"];
		$when = $message["edit_timestamp"];
		$is_reply = $message["is_reply"];
		$message_id = $message["message_id"];
		$text = $message["text"];
		$recent_12 = $message["recent_12"];
		$recent_24 = $message["recent_24"];

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

		echo "<form class='row highlightable' method='post' action='$postURL'>";
		echo "<input type='hidden' name='blackboard-item-id' value='$message_id'>";

		echo "<span class='cell chalk$font'>";

		// current item
		echo "<div class='blackboard-item $recent_tag'>";
		echo $text;
		echo "</div>";

		if ($editable)
		{
			// edit text box (usually hidden)
			echo "<div class='blackboard-item-edit'>";
			echo "<textarea name='blackboard-item-edit' class='chalk$my_font' rows=2 maxlength=200>";
			echo $text;
			echo "</textarea>";
			echo "</div>";
		}

		echo "</span>";

		if ($editable)
		{
			echo "<span class='cell buttons-cell'>";

			// buttons (visible on hover)
			echo "<div class='buttons'>";
			// TODO replies echo "<input type='submit' class='reply'  name='reply'  value='Reply'>";
			echo "<input type='submit' class='icons edit '  name='edit'   value='" . icon_edit() . "'>";
			echo "<input type='submit' class='icons delete' name='delete' value='" . icon_trash() . "'>";
			echo "<input type='submit' class='icons'        name='up'     value='" . icon_up() . "' $up_disabled>";
			echo "<input type='submit' class='icons'        name='down'   value='" . icon_down() . "' $down_disabled>";
			echo "</div>";

			// enter/cancel buttons (usually hidden)
			echo "<div class='buttons-enter'>";
			echo "<input type='submit' class='icons finish-edit default-button' name='finish-edit' value='" . icon_tick() . "'>";
			echo "<input type='submit' class='icons finish-edit cancel-button' name='cancel-edit' value='" . icon_cross() . "'>";
			echo "</div>";

			echo "</span>";
		}

		echo "</form>";

		if ($editable)
		{
			// reply box (usually hidden)
			echo "<form class='row blackboard-item-reply' method='post' action='$postURL'>";

			echo "<span class='cell chalk$font'>";
			echo "<input type='hidden' name='blackboard-item-id' value='$message_id'>";
			echo "<textarea name='blackboard-item-reply' class='chalk$my_font' rows=2 maxlength=200>";
			echo "</textarea>";
			echo "</span>";

			echo "<span class='cell buttons-cell'>";
			echo "<input type='submit' class='finish-reply default-button' name='finish-reply' value='Enter'>";
			echo "<input type='submit' class='finish-reply cancel-button' name='cancel-reply' value='Cancel'>";
			echo "</span>";

			echo "</form>";
		}
	}

	if ($editable)
	{
		// "add new" button
		echo "<form class='row' method='post' action='$postURL'>";
		echo "<span class='cell add-new'>";
		echo "<input type='submit' class='icons' name='new' value='" . icon_plus_in_circle() . "'>";
		echo "</span>";
		echo "</form>";

		// "add new" entry (usually hidden)
		echo "<form class='row add-new-edit' method='post' action='$postURL'>";
		// text area
		echo "<span class='cell'>";
		echo "<textarea name='blackboard-add-new' class='chalk$my_font' rows=2 maxlength=200>";
		echo "</textarea>";
		echo "</span>";
		// enter/cancel buttons
		echo "<span class='cell buttons-cell'>";
		echo "<div class='add-new-enter'>";
		echo "<input type='submit' class='icons finish-add-new default-button' name='finish-add-new' value='" . icon_tick() . "'>";
		echo "<input type='submit' class='icons finish-add-new cancel-button' name='cancel-add-new' value='" . icon_cross() . "'>";
		echo "</div>";
		echo "</span>";
		echo "</form>";
	}

	echo "</div>";
	echo "</div>";
?>

</body>
</html>

