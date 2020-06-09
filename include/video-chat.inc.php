<?php

include_once(__DIR__ . "/../config.inc.php");
include_once(__DIR__ . "/jitsi.inc.php");
include_once(__DIR__ . "/symbols.inc.php");



function draw_video_chat()
{
	?>
		<div id="video-home">

		<h2>
			Video rooms
		</h2>

	<?php

	if (canDeviceShowVideo())
	{
		echo '<div id="video-intro">';

		$nickname = getNickname();
		if ($nickname == "")
		{
			echo "To join a room, please set a nickname (at the top of the page).";
		}
		else
		{
			?>
				<p>
				Use headphones if possible.
				<p>
				No headphones? Consider muting when not speaking.
				<p>
				Poor internet? "Low bandwidth mode" does audio only.
			<?php
		}

		echo "</div>";

		?>
			<div id='video_rooms'>
			</div>
		<?php
			if ($nickname != "")
			{
		?>
				<div class="add-a-room">
					<h3>Or start your own video room ...</h3>
					<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
						<input type="text" name="room_name" size=50 maxlength=100 placeholder='Name of your new room'>
						<br>
						<textarea name='room_description' rows=2 maxlength=200 placeholder='Description of your new room'></textarea>
						<br>
						<label for="new_room_thumbnail">Optional image:</label>
						<input type="file" name="new_room_thumbnail" id="new_room_thumbnail" accept="image/*">
						<input type="submit" name='new_room' value="Add a new room">
					</form>	
				</div>
		<?php
			}
		?>
			<script type="text/javascript">

				$(document).ready(function()
				{
					loadRooms();

					// reload every 10 seconds
					setInterval(loadRooms, 10000);
				});

				function loadRooms()
				{
					$.ajax(
					{
						url: "video-chat-list.php",
						dataType: "html",
						success: function(html)
						{
							$("#video_rooms").html(html);
						},
					});
				}

				$('[name="room_name"]').keydown(function(e)
				{
					// enter moves to next box
					if (e.which == 13)
					{
						$('[name="room_description"]').focus();
						e.preventDefault();
					}
				});

				$('[name="room_description"]').keydown(function(e)
				{
					// enter submits form
					if (e.which == 13)
					{
						$('[name="new_room"]').click();
						e.preventDefault();
					}
				});

			</script>
		<?php
	}

	echo "</div>";
}


function handle_video_room_form()
{
	$me = getNickname();
	if ($me == "")
	{
		return;
	}

	if (is_variable_set("delete_room") && is_variable_set("delete_room_id"))
	{
		$room_id = sanitised_as_alphanumeric("delete_room_id");
		delete_video_room($room_id);
	}

	if (is_variable_set("new_room") && is_variable_set("room_name") && is_variable_set("room_description"))
	{
		$room_name = sanitised_as_alphanumeric_extended("room_name");
		$room_description = sanitised_as_text("room_description");
		$room_id = sanitised_as_alphanumeric_camelcase("room_name");

		if ($room_id != "" && $room_name != "")
		{
			new_video_room($me, $room_id, $room_name, $room_description);

			// was a thumbnail uploaded?
			if (isset($_FILES["new_room_thumbnail"]))
			{
				$filename = $_FILES["new_room_thumbnail"]["tmp_name"];

				// is it an image?
				if (($size = getimagesize($filename)) !== false)
				{
					list($width, $height, $type, $attr) = $size;

					$new_width = $width;
					$new_height = $height;

					// max height we need is 96px
					$max_height = 96;
					if ($height > $max_height)
					{
						$new_width = $width * $max_height / $height;
						$new_height = $max_height;
					}

					$original = imagecreatefromstring(file_get_contents($filename));
					$resized = imagecreatetruecolor($new_width, $new_height);
					imagecopyresampled($resized, $original, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					imagedestroy($original);
					global $VIDEO_ROOM_THUMBNAIL_UPLOAD_DIR;
					imagepng($resized, "$VIDEO_ROOM_THUMBNAIL_UPLOAD_DIR/$room_id.png");
					imagedestroy($resized);
				}
			}
		}
	}
}
