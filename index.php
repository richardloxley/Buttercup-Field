<?php

include_once("include/template.inc.php");
include_once("include/password.inc.php");
include_once("include/mobile.inc.php");
include_once("include/user-input.inc.php");
include_once("include/database.inc.php");
include_once("include/blackboard.inc.php");
include_once("include/text-chat.inc.php");
include_once("include/video-chat.inc.php");
include_once("include/active-users.inc.php");
include_once("include/special-events.inc.php");


$duplicate_nickname = "";


function handlePostVariables()
{
	if (is_variable_set("logout"))
	{
		delete_all_cookies();
		return;
	}

	save_as_cookie("password");
	save_as_cookie("app_installed");


	if (is_variable_set("duplicate_me") && is_variable_set("submitted_nickname"))
	{
		save_as_renamed_cookie("submitted_nickname", "nickname");
		user_is_active(getNickname());
	}
	else if (is_variable_set("duplicate_someone_else"))
	{
	}
	else if (is_variable_set("submitted_nickname"))
	{
		$submitted_nickname = sanitised_as_alphanumeric_extended("submitted_nickname");
		if ($submitted_nickname != "")
		{
			if (get_user($submitted_nickname) != null)
			{
				global $duplicate_nickname;
				$duplicate_nickname = $submitted_nickname;
			}
			else
			{
				save_as_renamed_cookie("submitted_nickname", "nickname");
				create_user(getClaimedNickname());
			}
		}
	}

	$me = getNickname();
	if ($me != "")
	{
		if (is_variable_set("new_blackboard"))
		{
			$new_board = new_blackboard($me);
			header("Location: /blackboard/$new_board");
			exit();
		}

		if (is_variable_set("delete-blackboard") && is_variable_set("board-id"))
		{
			$boardID = sanitised_as_int("board-id");
			delete_blackboard($boardID);
		}
	}

	handle_video_room_form();
}


function duplicateNickname()
{
	global $duplicate_nickname;
	return $duplicate_nickname;
}




// do this first as we need to set cookies before outputting headers
handlePostVariables();

draw_header("", "");

if (askForPasswordIfRequired())
{
	echo "<div id='home-header'>";
		echo "<div id='user-profile'>";
			draw_logout_button();
			draw_nickname_form();
		echo "</div>";
		echo "<h1>";
			echo $SITE_TITLE;
		echo "</h1>";
	echo "</div>";

	draw_blackboard_thumbs();
	draw_users_online();
	draw_special_events();
	draw_text_chat(0, $MAIN_TEXT_CHAT_TITLE, false);
	draw_video_chat();
}

draw_footer();
