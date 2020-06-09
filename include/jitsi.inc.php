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
	global $JITSI_FRAMERATE_IDEAL;
	global $JITSI_FRAMERATE_MAX;

	$config = array
	(
		// don't allow them to record it
		"localRecording" => array("enabled" => false),

		// everyone should have a display name, it's common courtesy :-)
		"requireDisplayName" => true,

		// if using the app, don't put the room name in the recently used list
		// as we use hidden room names, and we also don't want people joining
		// rooms without having gone through our site
		"doNotStoreRoom" => true,

		// put our room name at the top instead of the Jitsi room ID
		"subject" => $roomName,

		/////// performance related as Jitsi consumes very high CPU on older machines

		// don't show the blue dots for audio levels as the JavaScript rendering takes
		// up hige amounts of CPU just as we're trying to play the audio, making the
		// audio choppy
		"disableAudioLevels" => true,

		// whilst H264 supports hardware acceleration, it doesn't support simulcast
		// meaning everyone would get 720p video even if they have a slow connection
		"disableH264" => true,

		// limit framerates - this can give quite a big boost to performance without
		// impacting "talking heads" video too much
		"constraints" => array
		(
			"video" => array
			(
				"frameRate" => array
				(
					"ideal" => $JITSI_FRAMERATE_IDEAL,
					"max" => $JITSI_FRAMERATE_MAX
				)
			)
		)
	);

	return $config;
}


function interfaceOptions()
{
	$interface = array
	(
		// commented out options are items that are available but we've
		// chosen to remove either because they are undesirable for our
		// application or clutter up the UI unnecessarily for new users
		// without any real benefit
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

		// make the warnings about miuted microphones, etc, disappear after 5 sec
		"ENFORCE_NOTIFICATION_AUTO_DISMISS_TIMEOUT" => 5000,

		// make sure the toolbars are always visible as new users find it confusing
		// if they can't see the buttons
		"INITIAL_TOOLBAR_TIMEOUT" => 3600000,
		"TOOLBAR_TIMEOUT" => 3600000,
		"TOOLBAR_ALWAYS_VISIBLE" => true,

		// don't allow people to blur their backgrounds as it slows everything down
		// and isn't particularly good anyway
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

