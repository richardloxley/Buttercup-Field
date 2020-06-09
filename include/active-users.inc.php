<?php


function non_breaking_username($name)
{
	return preg_replace('/ /', '&nbsp;', $name);
}


function draw_users_online()
{
	?>
		<div id="whos-online">

		<h3>
		Online now
		</h3>

		<div id='online_now'>
		</div>

		<h3>
		Earlier today
		</h3>

		<div id='online_earlier'>
		</div>

		</div>

		<script type="text/javascript">

			$(document).ready(function()
			{
				loadActiveNow();
				loadActiveEarlier();

				// reload every 10 seconds
				setInterval(loadActiveNow, 10000);
				setInterval(loadActiveEarlier, 10000);
			});

			function loadActiveNow()
			{
				$.ajax(
				{
					url: "active-users-now.php",
					dataType: "html",
					success: function(html)
					{
						$("#online_now").html(html);
					},
				});
			}

			function loadActiveEarlier()
			{
				$.ajax(
				{
					url: "active-users-earlier.php",
					dataType: "html",
					success: function(html)
					{
						$("#online_earlier").html(html);
					},
				});
			}

		</script>
	<?php
}
