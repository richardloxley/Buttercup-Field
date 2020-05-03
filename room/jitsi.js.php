<?php

include_once("../jitsi.inc.php");
include_once("../debug.inc.php");


function makeJitsiJS($roomID, $roomName, $userNickname)
{
	makeJsDebug();

	?>

	<script>

	const nameUpdateURL = "<?php echo jitsiParticipantChangedCallbackURL();?>";

	// we don't send updates about people having left for a few seconds, in case they
	// only *appear* to be leaving because we've shut down the conference ourselves
	const LEAVING_TIMEOUT = 5000;


	var api;
	var timers = [];
	var my_id;
	var users = [];

	const domain = "<?php echo jitsiDomain();?>";
	const roomID = "<?php echo $roomID;?>";
	const roomName = "<?php echo $roomName;?>";
	const userNickname = "<?php echo $userNickname;?>";


	function startChat()
	{
		const options = <?php echo jitsiOptionsJS($roomID, $roomName, $userNickname);?>

		api = new JitsiMeetExternalAPI(domain, options);

		api.addListener('tileViewChanged', tileChanged);

		api.addListener('participantJoined', participantJoined);
		api.addListener('participantKickedOut', participantKickedOut);
		api.addListener('participantLeft', participantLeft);
		api.addListener('displayNameChange', displayNameChange);

		api.addListener('videoConferenceJoined', videoConferenceJoined);
		api.addListener('videoConferenceLeft', videoConferenceLeft);

		window.onbeforeunload = videoConferenceLeft;
	};


	// callbacks


	function tileChanged(newState)
	{
		// remove listener as we only want to change mode the first time - 
		// we're not going to stop the user manually changing back
		api.removeListener('tileViewChanged', tileChanged);

		if (!newState.enabled)
		{
			// if we haven't started up in tile view, enable it
			api.executeCommand('toggleTileView');
		}
	};


	function participantJoined(data)
	{
		debug("participantJoined");

		const id = data.id;
		const name = api.getDisplayName(id);

		debug(id);
		debug(name);

		fetch(nameUpdateURL + '?room=' + roomID + '&joined=' + encodeURIComponent(id) + '&name=' + encodeURIComponent(name));
	};


	function participantKickedOut(data)
	{
		debug("participantKickedOut");

		const id = data.kicked.id;

		debug(id);

		fetch(nameUpdateURL + '?room=' + roomID + '&kicked=' + encodeURIComponent(id));
	};


	function participantLeft(data)
	{
		debug("participantLeft");

		const id = data.id;

		debug(id);

		// we don't send updates about people having left for a few seconds, in case they
		// only *appear* to be leaving because we've shut down the conference ourselves
		timers.push(setTimeout(
			function()
			{
				fetch(nameUpdateURL + '?room=' + roomID + '&left=' + encodeURIComponent(id));
			},
			LEAVING_TIMEOUT
		));
	};


	function displayNameChange(data)
	{
		debug("displayNameChange");

		const id = data.id;
		const name = api.getDisplayName(id);

		debug(id);
		debug(name);

		fetch(nameUpdateURL + '?room=' + roomID + '&renamed=' + encodeURIComponent(id) + '&name=' + encodeURIComponent(name));
	};


	function videoConferenceJoined(data)
	{
		debug("videoConferenceJoined");

		my_id = data.id;
		const name = api.getDisplayName(my_id);

		debug(my_id);
		debug(name);

		fetch(nameUpdateURL + '?room=' + roomID + '&joined=' + encodeURIComponent(my_id) + '&name=' + encodeURIComponent(name));
	};


	function videoConferenceLeft(data)
	{
		debug("videoConferenceLeft");
		debug(data.roomName);

		// stop any timers for people apparently leaving
		for (var x = 0; x < timers.length; x++)
		{
			clearTimeout(timers[x]);
		}

		timers = [];

		// but notify that we've left (in case we were the last person in the room so
		// nobody else has noticed)
		fetch(nameUpdateURL + '?room=' + roomID + '&left=' + encodeURIComponent(my_id));
	};


	</script>

	<?php
}
