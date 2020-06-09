<?php
        include_once("../config.inc.php");
        include_once("../include/user-input.inc.php");
        include_once("../include/database.inc.php");
        include_once("../include/password.inc.php");
	include_once("../include/jitsi.js.inc.php");

	goHomeOnWrongPassword();

	$userNickname = getNickname();

	$roomID = "";
	$roomName = "";

	if (is_variable_set("room"))
	{
		$roomID = sanitised_as_alphanumeric("room");
		$roomName = getRoomNameFor($roomID);
	}

	draw_header($roomName, "room-body");

	if ($roomID == "" || $roomName == "")
	{
		echo "<p>";
		echo "This room doesn't exist";
	}
	else
	{
		echo "<script src='https://meet.jit.si/external_api.js'></script>";
		makeJitsiJS($roomID, $roomName, $userNickname);
		echo "<script> startChat(); </script>";
	}
?>
</body>
</html>
