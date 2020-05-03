<?php

include_once("config.inc.php");


function debug($message)
{
	global $DEBUG;

	if ($DEBUG)
	{
		error_log($message);
	}
}

function makeJsDebug()
{
	global $DEBUG;

	if ($DEBUG)
	{
		?>
		<script>
			function debug(message)
			{
				console.error(message);
			}
		</script>
		<?php
	}
	else
	{
		?>
		<script>
			function debug(message)
			{
			}
		</script>
		<?php
	}
}
