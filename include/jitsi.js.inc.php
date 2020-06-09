<?php

include_once(__DIR__ . "/jitsi.inc.php");
include_once(__DIR__ . "/debug.inc.php");


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
	var my_id = "";
	var users = {};

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

		if (my_id == "")
		{
			// our ID isn't set yet, so that means we've just joined ourself, and Jitsi
			// is telling us all the other people in the room, so let's just store them
			// in a list until we've got all of them
			users[id] = name;
		}
		else
		{
			// tell the server about a new user
			fetch(nameUpdateURL + '?room=' + roomID + '&myid=' + encodeURIComponent(my_id) + '&joined=' + encodeURIComponent(id) + '&name=' + encodeURIComponent(name));
		}
	};


	function participantKickedOut(data)
	{
		debug("participantKickedOut");

		const id = data.kicked.id;

		debug(id);

		// tell the server that someone's left
		fetch(nameUpdateURL + '?room=' + roomID + '&myid=' + encodeURIComponent(my_id) + '&kicked=' + encodeURIComponent(id));

		// Was it me that was kicked out?  If so, reset my user ID as I'm about to get loads of notifications that
		// everyone else has apparently left, and I don't want to send those to the server!
		if (id == my_id)
		{
			my_id = "";
		}
	};


	function participantLeft(data)
	{
		// if my ID isn't set, it's because I'm not yet in a meeting, or I'm in the process of being kicked
		// out of a meeting, so we don't want to send anything to the server
		if (my_id == "")
		{
			return;
		}

		debug("participantLeft");

		const id = data.id;

		debug(id);

		// we don't send updates about people having left for a few seconds, in case they
		// only *appear* to be leaving because we've shut down the conference ourselves
		timers.push(setTimeout(
			function()
			{
				// tell the server that someone's left
				fetch(nameUpdateURL + '?room=' + roomID + '&myid=' + encodeURIComponent(my_id) + '&left=' + encodeURIComponent(id));
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

		// tell the server that someone's changed their name
		fetch(nameUpdateURL + '?room=' + roomID + '&myid=' + encodeURIComponent(my_id) + '&renamed=' + encodeURIComponent(id) + '&name=' + encodeURIComponent(name));
	};


	function videoConferenceJoined(data)
	{
		debug("videoConferenceJoined");

		my_id = data.id;
		const name = api.getDisplayName(my_id);

		debug(my_id);
		debug(name);

		users[my_id] = name;

		// We've just joined, and before we got the "videoConferenceJoined" notification we got "participantJoined"
		// notifications for all the other users in the room.  So we can now send a definitive list of users to the server.
		// This helps "reset" the server's view of who's in the room, as we can have phantom users left if the last people
		// to leave the room don't have JavaScript enabled (e.g. mobile app users).

		debug(JSON.stringify(users));

		fetch(nameUpdateURL + '?room=' + roomID + '&myid=' + encodeURIComponent(my_id) + '&allusers=' + encodeURIComponent(JSON.stringify(users)));
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
		fetch(nameUpdateURL + '?room=' + roomID + '&myid=' + encodeURIComponent(my_id) + '&left=' + encodeURIComponent(my_id));
	};


	</script>

	<?php
}
