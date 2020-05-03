<?php

function isIOS()
{
	$iPod    = (stripos($_SERVER['HTTP_USER_AGENT'],"iPod") !== false);
	$iPhone  = (stripos($_SERVER['HTTP_USER_AGENT'],"iPhone") !== false);
	$iPad    = (stripos($_SERVER['HTTP_USER_AGENT'],"iPad") !== false);

	// iPadOS isn't caught by the above, but Jitsi seems to work in iOS 13 Safari anyway.
	// If we want to promote app we could investigate using JS to see if the browser supports multi-touch to identify it
	// https://stackoverflow.com/questions/58019463/how-to-detect-device-name-in-safari-on-ios-13-while-it-doesnt-show-the-correct
	// $safari  = stripos($_SERVER['HTTP_USER_AGENT'],"Safari");
	// JS: let isIOS = /iPad|iPhone|iPod/.test(navigator.platform) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)

	return ($iPod || $iPhone || $iPad);
}


function isAndroid()
{
	$android = (stripos($_SERVER['HTTP_USER_AGENT'],"Android") !== false);

	return $android;
}


function canDeviceShowVideo()
{
	$url = "";
	$icon = "";

	$ios = isIOS();
	$android = isAndroid();

	if (!isAppInstalled() && ($ios || $android))
	{
		if ($ios)
		{
			$url = "https://itunes.apple.com/us/app/jitsi-meet/id1165103905";
			$icon = "images/ios-store.svg";
		}
		else
		{
			$url = "https://play.google.com/store/apps/details?id=org.jitsi.meet";
			$icon = "images/google-store.png";
		}

		?>
			<div class="mobile-banner">
				To join the video chat on this device, you'll need to download the mobile app:
				<br>
				<a href="<?php echo $url;?>" rel="noopener" target="_blank">
					<img src="<?php echo $icon;?>">
				</a>
				<br>
				Once you've installed the app, come back to this page and click this button:
				<br>
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<input type="submit" name="app_installed" value="I've installed the app">
				</form>
				(Or if you don't want to install an app you can use a browser on a Windows/Mac/Linux computer.)
			</div>
		<?php

		return false;
	}
	else
	{
		return true;
	}
}
