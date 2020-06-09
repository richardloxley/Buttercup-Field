<?php

include_once(__DIR__ . "/../config.inc.php");
include_once(__DIR__ . "/template.inc.php");
include_once(__DIR__ . "/user-input.inc.php");


function wrongPassword()
{
	global $PASSWORD;

	$password = getPassword();
	return (stripos($password, $PASSWORD) === false);
}


function goHomeOnWrongPassword()
{
	if (wrongPassword())
	{
		header("Location: /");
		exit();
	}
}


function askForPasswordIfRequired()
{
	global $SITE_GREETING;
	global $PASSWORD_HINT;
	global $CONTACT_US_HTML;

	$password = getPassword();

	if (wrongPassword())
	{
		// no password or wrong password
		?>
			<div id="login-form">

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
			echo "<p class='password-wrong'>";
			echo "Sorry - that's not the password.";
			echo " ";
			echo $CONTACT_US_HTML;
		}

		echo "</div>";

		return false;
	}
	else
	{
		return true;
	}
}


function draw_logout_button()
{
	?>
		<div id="logout">
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<input type="submit" name="logout" value="Logout">
			</form>
		</div>
	<?php
}
