<?php

include_once(__DIR__ . "/symbols.inc.php");
include_once(__DIR__ . "/user-input.inc.php");


function draw_header($title, $body_class)
{
	if ($title == "")
	{
		global $SITE_TITLE;
		$title = $SITE_TITLE;
	}

	?>
		<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title><?php echo $title;?></title>
			<link rel="stylesheet" href="/style.css?v=12" type="text/css">
			<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
			<?php icons_header(); ?>
			<?php draw_touchscreen_detection(); ?>
		</head>
		<body class='<?php echo $body_class;?>'>
	<?php
}


function draw_footer()
{
	// only draw banner when we're logged in
	if (getNickname() != "")
	{
		?>
		<div class="footer">
			Powered by <a href="https://github.com/richardloxley/Buttercup-Field">Buttercup Field</a>
			:
			<a href="/credits/">Credits</a>
		</div>
		<?php
	}

	?>
		</body>
		</html>
	<?php
}


function draw_touchscreen_detection()
{
	?>
		<script type="text/javascript">

		$(document).ready(function()
		{
			if ('ontouchstart' in document.documentElement)
			{
				$('body').addClass("touchscreen");
			}
		});

		</script>
	<?php
}


function draw_nickname_form()
{
	echo "<div id='nickname'>";

	if (duplicateNickname() != "")
	{
		?>
			Someone else has already used that nickname.  Was that you on a different computer?
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<input type="hidden" name="submitted_nickname" value="<?php echo duplicateNickname();?>">
			<input type="submit" name="duplicate_me" value="Yes that was me!">
			<input type="submit" name="duplicate_someone_else" value="No - I'll choose another nickname">
			</form>
		<?php
	}
	else if (edittingNickname() || getNickname() == "")
	{
		?>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			Please choose a nickname:
			<br>
			<input type="text" name="submitted_nickname" value="<?php echo getNickname();?>" size=25 maxlength=100>
			<input type="submit" value="Enter">
			</form>
		<?php
	}
	else
	{
		?>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			Welcome <?php echo getNickname();?>
			<br>
			<input type="submit" name="edit_nickname" value="Change nickname">
			</form>
		<?php
	}

	echo "</div>";
}


