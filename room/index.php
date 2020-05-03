<?php
        include_once("../post-variables.inc.php");
        include_once("../database.inc.php");
        include_once("../config.inc.php");
	include_once("jitsi.js.php");

	$userNickname = getNickname();

	$roomID = "";
	$roomName = "";

	if (isset($_GET["room"]))
	{
		$roomID = sanitiseRoomID($_GET["room"]);
		$roomName = getRoomNameFor($roomID);
	}

?>
	<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $roomName;?></title>
		<link rel="stylesheet" href="room.css" type="text/css">
	</head>
	<body>
<?php

	if ($roomID == "" || $roomName == "")
	{
		echo "<p>";
		echo "This room doesn't exist";
	}
	else
	{
		?>
			<div class='thumbnail'>
				<img src="<?php echo $ROOM_DEFAULT_IMAGE;?>">
			</div>

			<script src='https://meet.jit.si/external_api.js'></script>
		<?php
			makeJitsiJS($roomID, $roomName, $userNickname)
		?>
			<script>
				startChat();
			</script>
		<?php
	}
?>
</body>
</html>
