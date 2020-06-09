<?php

function draw_text_chat($room_id, $room_name, $alert)
{
	// inspired by https://code.tutsplus.com/tutorials/how-to-create-a-simple-web-based-chat-application--net-5931

        ?>
		<div id="chat-home">

		<div class="chat-full-screen">
			<a href="/chat/" target="_blank">
				<span class="icon-description">
					Full screen
				</span>
				<span class="icons">
					<?php echo icon_open_external(); ?>
				</span>
			</a>
		</div>

                <h2>
                        <?php echo $room_name; ?>
                </h2>

		<div id="chat">
			<?php
				if (getNickname() == "")
				{
					?>
					<p>
					To send text messages, please set a nickname (at the top of the page).
					<?php
				}
			?>

			<div id="chatbox-outer">
			<div id="chatbox-inner">
			<div id="chatbox">
			</div>
			</div>
			</div>

			<?php
				if (getNickname() != "")
				{
					?>
					<form name="message" action="" >
						<input name="message_text" type="text" id="message_text" placeholder="Enter a chat message"/>
						<input name="message_send" type="submit" id="message_send" value="Send"/>
						<input name="message_thumbs_up" type="submit" id="message_thumbs_up" class="icons" value="<?php echo icon_thumbs_up();?>"/>
					</form>
					<?php
				}
			?>
		</div>

		</div>

		<script type="text/javascript">

			$(document).ready(function()
			{
				loadLog();

				// reload every second
				setInterval(loadLog, 1000);
			});

			// if user submits the form
			$("#message_send").click(function()
			{
				var clientmsg = $("#message_text").val();
				$.post("/text-chat-post.php", {chat_room: <?php echo $room_id;?>, text: clientmsg});				
				$("#message_text").attr("value", "");
				loadLog();
				return false;
			});

			audioCtx = new (window.AudioContext || window.webkitAudioContext)();

			function beep(volume, frequency, duration)
			{
				var oscillator = audioCtx.createOscillator();
				var gainNode = audioCtx.createGain();

				oscillator.connect(gainNode);
				gainNode.connect(audioCtx.destination);

				gainNode.gain.value = volume;
				oscillator.frequency.value = frequency;
				oscillator.type = 0;

				oscillator.start();
				setTimeout(function(){oscillator.stop();}, duration);  
			};


			$("#message_thumbs_up").click(function()
			{
				$.post("/text-chat-post.php", {chat_room: <?php echo $room_id;?>, text: ":thumb:"});				
				loadLog();
				$(this).blur();
				//$("#message_text").focus();
				return false;
			});

			var last_message = 0;

			function loadLog()
			{
				$.ajax(
				{
                                        // ask for any new messages (since the last one we saw)
					url: "/text-chat-display.php?chat_room=<?php echo $room_id;?>&since=" + last_message,
					dataType: "json",
					success: function(json)
					{
						// check we actually have new messages (the interval timer means we could have
						// two ajax queries simulataneously if the user posts a new message, so we don't
						// want to append the new message twice)
						if (json.last != last_message)
						{
							// remember where we got to so we don't request it again
							last_message = json.last;
							// append new messages to the chat box
							$("#chatbox").append(json.html);
							// scroll to bottom
							$('#chatbox').scrollTop($('#chatbox')[0].scrollHeight - $('#chatbox')[0].clientHeight);

							<?php
								if ($alert)
								{
									?>
										// beep to alert of new messages
										beep(0.2, 300, 50);
									<?php
								}
							?>
						}
					},
				});
			}

		</script>
	<?php
}


