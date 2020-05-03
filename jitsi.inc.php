<?php


function jitsiDomain()
{
	return 'meet.jit.si';
}


function jitsiParticipantChangedCallbackURL()
{
	return "/jitsi-participants-changed-callback.php";
}


function jitsiDeeplinkIos($roomID, $roomName, $nickname)
{
	return "org.jitsi.meet://" . jitsiDomain() . jitsiOptionsAsURL($roomID, $roomName, $nickname);
}


function jitsiDeeplinkAndroid($roomID, $roomName, $nickname)
{
	return "intent://" . jitsiDomain() . jitsiOptionsAsURL($roomID, $roomName, $nickname) . "#Intent;scheme=org.jitsi.meet;package=org.jitsi.meet;end";
}


function configOptions($roomName)
{
	$config = array
	(
		"localRecording" => array("enabled" => false),
		"requireDisplayName" => true,
		"subject" => $roomName
	);

	return $config;
}


function interfaceOptions()
{
	$interface = array
	(
		"TOOLBAR_BUTTONS" => array
		(
			'microphone',
			'camera',
			'closedcaptions',
			'desktop',
			'fullscreen',
			'fodeviceselection',
			'hangup',
			'profile',
			//'info',
			'chat',
			//'recording',
			//'livestreaming',
			'etherpad',
			//'sharedvideo',
			'settings',
			//'raisehand',
			'videoquality',
			'filmstrip',
			'invite',
			'feedback',
			'stats',
			'shortcuts',
			'tileview',
			//'videobackgroundblur',
			'download',
			'help',
			'mute-everyone',
			'e2ee'
		),

		"SETTINGS_SECTIONS" => array
		(
			'devices',
			'language',
			//'moderator',
			//'calendar',
			'profile'
		),

		"ENFORCE_NOTIFICATION_AUTO_DISMISS_TIMEOUT" => 10000,

		"DISABLE_VIDEO_BACKGROUND" => true
	);

	return $interface;
}


function jitsiOptionsJS($roomID, $roomName, $nickname)
{
	global $JITSI_UNIQUE_ROOM_PREFIX;

	$options = array
	(
		"roomName" => $JITSI_UNIQUE_ROOM_PREFIX . $roomID,
		"userInfo" => array("displayName" => $nickname),
		"configOverwrite" => configOptions($roomName),
		"interfaceConfigOverwrite" => interfaceOptions()
	);

	return json_encode($options);
}


function jitsiOptionsAsURL($roomID, $roomName, $nickname)
{
	global $JITSI_UNIQUE_ROOM_PREFIX;

	$url = "/" . $JITSI_UNIQUE_ROOM_PREFIX . $roomID . "#jitsi_meet_external_api_id=0";

	$url .= "&userInfo.displayName=" . rawurlencode(json_encode($nickname));

	foreach (configOptions($roomName) as $key => $value)
	{
		$url .= "&config." . $key . "=" . rawurlencode(json_encode($value));
	}

	foreach (interfaceOptions() as $key => $value)
	{
		$url .= "&interfaceConfig." . $key . "=" . rawurlencode(json_encode($value));
	}

	return $url;
}

