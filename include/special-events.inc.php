<?php


function draw_special_events()
{
	// examples of typical special events that can be customised
	// and turned on and off by editing this file

	// draw_event_birthday();
	// draw_event_tournament();
	// draw_event_image_gallery();
	// draw_event_livestream();
}


function draw_event_image_gallery()
{
	?>
		<div id="special-event">

			<h2 style='margin-bottom:10px;'>
				Image gallery
			</h2>

			<p>
			Click to embiggen

			<div class="event-image-gallery">
	<?php
				$upload_dir = "/uploads";

				$images =
				[
					"Example 1" => "gallery_example_1.jpg",
					"Example 2" => "gallery_example_2.jpg"
				];

				foreach ($images as $label => $image)
				{
					$url = "$upload_dir/$image";
					echo "<figure>";
					echo "<a href='$url'>";
					echo "<img src='$url'>";
					echo "<figcaption>$label</figcaption>";
					echo "</a>";
					echo "</figure>";
				}
	?>
			</div>

			<div class="event-image-gallery-end">
			</div>
		</div>
	<?php
}


function draw_event_tournament()
{
	handle_event_tournament_form();

	// query string to force image to update if page refreshes, even if cached 
	$image_url = tournament_filename() . "?r=" . time();

	?>
		<div id="special-event">

			<h2>
				Tournament
			</h2>

			<p>
				Tournament structure &mdash; click for full size:

			<div class="event-image">
				<a href='<?php echo $image_url;?>'>
					<img src='<?php echo $image_url;?>'>
				</a>
			</div>

			<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<label for="tournament_image">Or upload updated version:</label>
			<input type="file" name="tournament_image" id="tournament_image" accept="image/*">
			<input type="submit" name='new_tournament_image' value="Replace image">
			</form>
		</div>
	<?php
}


function tournament_filename()
{
	global $IMAGE_UPLOAD_DIR;
	return "$IMAGE_UPLOAD_DIR/tournament.jpg";
}


function handle_event_tournament_form()
{
	$max_dimension = 1000;

	$destination_filename = tournament_filename();

	$me = getNickname();
	if ($me == "")
	{
		return;
	}

	if (is_variable_set("new_tournament_image") && isset($_FILES["tournament_image"]))
	{
		$filename = $_FILES["tournament_image"]["tmp_name"];

		// is it an image?
		if ($filename != "" && ($size = getimagesize($filename)) !== false)
		{
			list($width, $height, $type, $attr) = $size;

			$new_width = $width;
			$new_height = $height;

			if ($width > $max_dimension || $height > $max_dimension)
			{
				$ratio = $width / $height;
				if ($ratio > 1)
				{
					$new_width = $max_dimension;
					$new_height = $max_dimension / $ratio;
				}
				else
				{
					$new_width = $max_dimension * $ratio;
					$new_height = $max_dimension;
				}
			}

			$original = imagecreatefromstring(file_get_contents($filename));
			$resized = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($resized, $original, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagedestroy($original);
			imagejpeg($resized, $destination_filename);
			imagedestroy($resized);
		}
	}
}

function draw_event_birthday()
{
	?>
		<div id="special-event">

		<h2 style='margin-bottom:10px;'>
			Happy Birthday!
		</h2>

		<center>
		<img src='/uploads/birthday.jpg' style='max-height:350px;max-width:100%;width:auto;height:auto;'>
		</center>

		</div>
	<?php
}


function draw_event_livestream()
{
	?>
		<div id="special-event">

		<h2>
			Livestream Party &mdash; 8pm tonight
		</h2>

		<div id="event-intro">
			Watch on TV: 8pm - 10pm.
			<p>
			Or on <a href="https://www.youtube.com/" target="_blank" rel='noreferrer'>YouTube</a>
			<p>
			Join the <a href="/event/" target="_blank">special event text chat!</a>
		</div>

		</div>
	<?php
}


