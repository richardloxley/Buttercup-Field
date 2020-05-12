<?php

include_once("template.inc.php");
include_once("password.inc.php");
include_once("mobile.inc.php");
include_once("user-input.inc.php");
include_once("database.inc.php");
include_once("blackboard.inc.php");
include_once("text-chat.inc.php");
include_once("video-chat.inc.php");


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
				create_user(getNickname());
			}
		}
	}

	$me = getNickname();
	if ($me != "")
	{
		if (is_variable_set("new_blackboard"))
		{
			new_blackboard($me);
		}

		if (is_variable_set("delete-blackboard") && is_variable_set("board-id"))
		{
			$boardID = sanitised_as_int("board-id");
			delete_blackboard($boardID);
		}
	}

}


function duplicateNickname()
{
	global $duplicate_nickname;
	return $duplicate_nickname;
}




// do this first as we need to set cookies before outputting headers
handlePostVariables();

draw_header();

if (gotPassword())
{
	draw_nickname_form();
	draw_blackboard_thumbs();
	draw_users_online();
	draw_text_chat();
	draw_video_chat();
}

draw_footer();
