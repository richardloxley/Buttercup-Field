<?php

function draw_text_chat()
{
	// inspired by https://code.tutsplus.com/tutorials/how-to-create-a-simple-web-based-chat-application--net-5931

        ?>
                <h2>
                        Text chat
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

			<div id="chatbox">
			</div>

			<?php
				if (getNickname() != "")
				{
					?>
					<form name="message" action="" >
						<input name="usermsg" type="text" id="usermsg"/>
						<input name="submitmsg" type="submit"  id="submitmsg" value="Send"/>
					</form>
					<?php
				}
			?>
		</div>

		<script type="text/javascript">

			$(document).ready(function()
			{
				loadLog();
			});

			// if user submits the form
			$("#submitmsg").click(function()
			{
				var clientmsg = $("#usermsg").val();
				$.post("text-chat-post.php", {text: clientmsg});				
				$("#usermsg").attr("value", "");
				loadLog();
				return false;
			});

			function loadLog()
			{
				var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20; // scroll height before the request

				$.ajax(
				{
					url: "text-chat-display.php",
					cache: false,
					success: function(html)
					{		
						$("#chatbox").html(html); // insert chat log into the #chatbox div				

						// auto-scroll			
						var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20; // scroll height after the request
						if (newscrollHeight > oldscrollHeight)
						{
							$("#chatbox").animate({scrollTop: newscrollHeight}, 'normal'); // autoscroll to bottom of div
						}				
					},
				});
			}

			// reload every second
			setInterval(loadLog, 1000);

		</script>
	<?php
}


