<?php

include_once("include/user-input.inc.php");
include_once("include/database.inc.php");
include_once("include/password.inc.php");
include_once("include/symbols.inc.php");

goHomeOnWrongPassword();

$nickname = getNickname();

if ($nickname != "")
{
	$chat_room = 0;

	if (is_variable_set('chat_room'))
	{
		$chat_room = sanitised_as_int('chat_room');
	}

	if (is_variable_set('text'))
	{
		$message = sanitised_as_text('text');

		$message = preg_replace('/:thumb:/', symbolThumbsUp(), $message);
		$message = preg_replace('/:facepalm:/', symbolFacePalm(), $message);
		$message = preg_replace('/\B:-\)\B/', symbolSmile(), $message);
		$message = preg_replace('/\B:\)\B/', symbolSmile(), $message);
		$message = preg_replace('/:-D/', symbolGrin(), $message);
		$message = preg_replace('/:D/', symbolGrin(), $message);
		$message = preg_replace('/\B:-\(\B/', symbolFrown(), $message);
		$message = preg_replace('/\B:\(\B/', symbolFrown(), $message);
		$message = preg_replace('/\B;-\)\B/', symbolWink(), $message);
		$message = preg_replace('/\B;\)\B/', symbolWink(), $message);

		if ($message != "")
		{
			post_text_chat($chat_room, $nickname, $message);
		}
	}
}

