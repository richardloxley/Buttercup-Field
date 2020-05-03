<?php

include_once("template.inc.php");
include_once("config.inc.php");


function gotPassword()
{
	global $SITE_GREETING;
	global $PASSWORD;
	global $PASSWORD_HINT;
	global $CONTACT_US_URL;

	$password = getPassword();

	if (stripos($password, $PASSWORD) === false)
	{
		// no password or wrong password
		?>
			<h1>
			<?php echo $SITE_GREETING;?>
			</h1>
			<p>
			Please enter the password to continue.
			<p class='password-hint'>
			<?php echo $PASSWORD_HINT;?>
			<p>
			<form method="post" autocomplete="off" autocorrect="off" autocapitalize="off" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<input type="text" name="password" value="" size=20 maxlength=20>
			<input type="submit" value="Enter">
			</form>
		<?php

		if ($password != "")
		{
			// wrong password
			?>
				<p class='password-wrong'>
				Sorry - that's not the password.  If you need help, please <a href="<?php echo $CONTACT_US_URL?>">contact us</a>.
			<?php
		}

		return false;
	}
	else
	{
		// logout button
		?>
			<div class="logout">
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<input type="submit" name="logout" value="Logout">
				</form>
			</div>
		<?php

		return true;
	}
}
