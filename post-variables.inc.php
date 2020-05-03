<?php

include_once("config.inc.php");


$password = "";
$nickname = "";
$edit_nickname = false;
$app_installed = false;


function handlePostVariables()
{
	if (isset($_POST["logout"]))
	{
		deleteAllVariables();
		return;
	}


	global $password;

	if (isset($_POST["password"]))
	{
		$password = $_POST["password"];
		setcookie("password", $password, time() + 31536000, "/");
	}
	else if (isset($_COOKIE["password"]))
	{
		$password = $_COOKIE["password"];
	}


	global $nickname;
	global $NICKNAME_REGEX;

	if (isset($_POST["nickname"]))
	{
		$nickname = preg_replace($NICKNAME_REGEX, '', $_POST["nickname"]);
		setcookie("nickname", $nickname, time() + 31536000, "/");
	}
	else if (isset($_COOKIE["nickname"]))
	{
		$nickname = $_COOKIE["nickname"];
	}

	global $edit_nickname;

	if (isset($_POST["edit_nickname"]))
	{
		$edit_nickname = true;
	}

	global $app_installed;

	if (isset($_POST["app_installed"]))
	{
		$app_installed = true;
		setcookie("app_installed", "yes", time() + 31536000, "/");
	}
	else if (isset($_COOKIE["app_installed"]))
	{
		$app_installed = true;
	}
}


function getPassword()
{
	global $password;
	return $password;
}


function getNickname()
{
	global $nickname;

	if ($nickname == "" && isset($_COOKIE["nickname"]))
	{
		$nickname = $_COOKIE["nickname"];
	}

	return $nickname;
}


function edittingNickname()
{
	global $edit_nickname;
	return $edit_nickname;
}


function isAppInstalled()
{
	global $app_installed;
	return $app_installed;
}


function deleteAllVariables()
{
	global $password;

	$password = "";

	$past = time() - 3600;
	foreach ($_COOKIE as $key => $value)
	{
		setcookie($key, "", $past);
	}
}
