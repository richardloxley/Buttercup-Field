<?php


function draw_header()
{
	global $SITE_TITLE;

	?>
		<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title><?php echo $SITE_TITLE;?></title>
			<link rel="stylesheet" href="style.css" type="text/css">
			<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
		</head>
		<body>
	<?php
}


function draw_footer()
{
	// only draw banner when we're logged in
	if (getNickname() != "")
	{
		?>
		<div class="footer">
			Powered by <a href="https://github.com/richardloxley/Buttercup-Field">Buttercup Field</a> by <a href="https://www.richardloxley.com/">Richard Loxley</a>
		</div>
		<?php
	}

	?>
		</body>
		</html>
	<?php
}


function draw_nickname_form()
{
	if (duplicateNickname() != "")
	{
		?>
			<p>
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
			<p>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			Please choose a nickname: <input type="text" name="submitted_nickname" value="<?php echo getNickname();?>" size=50 maxlength=100>
			<input type="submit" value="Enter">
			</form>
		<?php
	}
	else
	{
		?>
			<p>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			Welcome <?php echo htmlentities(getNickname(), ENT_QUOTES);?>
			<input type="submit" name="edit_nickname" value="Change nickname">
			</form>
		<?php
	}
}


function draw_users_online()
{
	?>
		<h2>
		Users
		</h2>

		<h3>
		Online now
		</h3>
	<?php

	$someone_chatting = false;
	$someone_on_video = false;
	$someone_on_mobile = false;

	$users = get_users_active();

	foreach ($users as $name => $options)
	{
		echo '<span class=active-user>';
		echo $name;

		if ($options["chatting"])
		{
			echo " " . symbolSpeech();
			$someone_chatting = true;
		}

		if ($options["video"])
		{
			echo " " . symbolCinema();
			$someone_on_video = true;
		}

		if ($options["mobile"])
		{
			echo " " . symbolMobilePhone();
			$someone_on_mobile = true;
		}

		echo '</span>';
	}

	if ($someone_chatting || $someone_on_video || $someone_on_mobile)
	{
		echo "<p>";
	}

	if ($someone_chatting)
	{
		echo symbolSpeech() . " = using text chat ";
	}

	if ($someone_on_video)
	{
		echo symbolCinema() . " = in a video room ";
	}

	if ($someone_on_mobile)
	{
		echo symbolMobilePhone() . " = on a mobile device";
	}


	?>
		<h3>
		Earlier today
		</h3>
	<?php

	$users = get_users_active_earlier();

	foreach ($users as $name)
	{
		echo '<span class=inactive-user>';
		echo $name;
		echo '</span>';
	}
}
