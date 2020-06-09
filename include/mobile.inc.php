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
	$description1 = "";
	$description2 = "";

	$ios = isIOS();
	$android = isAndroid();

	if (!isAppInstalled() && ($ios || $android))
	{
		if ($ios)
		{
			global $JITSI_MOBILE_APP_URL_IOS;
			$url = $JITSI_MOBILE_APP_URL_IOS;
			$description1 = "Download on the";
			$description2 = "App Store";
		}
		else
		{
			global $JITSI_MOBILE_APP_URL_ANDROID;
			$url = $JITSI_MOBILE_APP_URL_ANDROID;
			$description1 = "Download from";
			$description2 = "Google Play";
		}

		?>
			<div class="mobile-banner">
				To join the video chat on this device, you'll need to download the mobile app:
				<br>
				<div class='app-store-button-wrapper'>
					<a href="<?php echo $url;?>" rel="noopener" target="_blank">
						<div class='app-store-button'>
							<div class='app-store-button-line1'>
								<?php echo $description1;?>
							</div>
							<div class='app-store-button-line2'>
								<?php echo $description2;?>
							</div>
						</div>
					</a>
				</div>
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
