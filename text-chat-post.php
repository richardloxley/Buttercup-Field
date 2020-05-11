<?php

include_once("user-input.inc.php");
include_once("database.inc.php");


$nickname = getNickname();

if ($nickname != "")
{
	if (is_variable_set('text'))
	{
		$message = sanitised_as_text('text');
		post_text_chat($nickname, $message);
	}
}

