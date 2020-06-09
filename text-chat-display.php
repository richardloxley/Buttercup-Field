<?php

include_once("include/user-input.inc.php");
include_once("include/database.inc.php");
include_once("include/password.inc.php");


function parse_urls($text)
{
	$regex = "((https?|ftp)\:\/\/)?"; // SCHEME 
	$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass 
	$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP 
	$regex .= "(\:[0-9]{2,5})?"; // Port 
	$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path 
	$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query 
	$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor 

	return preg_replace("/($regex)/i", "<a href='$1' target='_blank' rel='noreferrer'>$1</a>", $text);
}

goHomeOnWrongPassword();

$myNickname = getNickname();

if ($myNickname != "")
{
	user_is_active($myNickname);
}


$chat_room = 0;

if (is_variable_set('chat_room'))
{
	$chat_room = sanitised_as_int('chat_room');
}

// if not present it will default to 0, which will give us all messages
$since_message_id = sanitised_as_int("since");

$messages = get_text_chats($chat_room, $since_message_id);

$last_id = -1;
$html = "";

foreach ($messages as $message)
{
	$posted = $message['posted'];
	$nickname = $message['nickname'];
	$text = $message['message'];
	$text = parse_urls($text);

	$last_id = $message['message_id'];

	$timestamp = strtotime($posted);
	$human_time = date("D H:i", $timestamp);

	if ($nickname == $myNickname)
	{
		// don't display name since it's me
		$html .= "<div class='my-text-message'>";
	}
	else
	{
		$html .= "<div class='text-message'>";
		$html .= "<div class='post-name'>";
		$html .= $nickname;
		$html .= "</div>";
	}

	$html .= "<div class='post-message'>";
	$html .= "<div class='speech-arrow'></div>";
	$html .= $text;
	$html .= "<div class='post-time-wrapper'>";
	$html .= "<div class='post-time'>";
	$html .= $human_time;
	$html .= "</div>";
	$html .= "</div>";
	$html .= "</div>";


	$html .= "</div>";

	$html .= "<div class='end-of-message'>";
	$html .= "</div>";
}

if ($last_id < 0)
{
	header("HTTP/1.1 304 Not Modified");
}
else
{
	$result = [ "last" => $last_id, "html" => $html ];

	header('Content-Type: application/json');
	echo json_encode($result);
}
