<?php

include_once(__DIR__ . "/../config.inc.php");
include_once(__DIR__ . "/database.inc.php");



function is_variable_set($var_name)
{
	if (isset($_GET[$var_name]) || isset($_POST[$var_name]) || isset($_COOKIE[$var_name]))
	{
		return true;
	}
	else
	{
		return false;
	}
}


function get_unsafe_variable($var_name)
{
	if (isset($_GET[$var_name]))
	{
		return $_GET[$var_name];
	}
	else if (isset($_POST[$var_name]))
	{
		return $_POST[$var_name];
	}
	else if (isset($_COOKIE[$var_name]))
	{
		return $_COOKIE[$var_name];
	}
	else
	{
		return false;
	}
}


function sanitised_as_int($var_name)
{
	if (is_variable_set($var_name))
	{
		return intval(get_unsafe_variable($var_name));
	}
	else
	{
		return 0;
	}
}


function sanitised_as_text($var_name)
{
	if (is_variable_set($var_name))
	{
		return stripslashes(htmlspecialchars(get_unsafe_variable($var_name), ENT_QUOTES));
	}
	else
	{
		return "";
	}
}


function to_alphanumeric($unsafe_value)
{
	return preg_replace('/[^a-zA-Z0-9]/', '', $unsafe_value);
}


function sanitised_as_alphanumeric($var_name)
{
	if (is_variable_set($var_name))
	{
		return to_alphanumeric(get_unsafe_variable($var_name));
	}
	else
	{
		return "";
	}
}


function sanitised_as_alphanumeric_camelcase($var_name)
{
	if (is_variable_set($var_name))
	{
		return to_alphanumeric(ucwords(get_unsafe_variable($var_name)));
	}
	else
	{
		return "";
	}
}


function to_alphanumeric_extended($unsafe_value)
{
	// allow alphanumeric, space, -^.,()[]{}*+?_!=
	$sanitised = preg_replace('/[^-\^\.\,\(\)\[\]\{\}\*\+\?_!= a-zA-Z0-9]/', '', $unsafe_value);

	// but don't allow only whitespace!
	$stripped = preg_replace('/\s/', '', $sanitised);

	if ($stripped == "")
	{
		return "";
	}
	else
	{
		return $sanitised;
	}
}


function sanitised_as_alphanumeric_extended($var_name)
{
	if (is_variable_set($var_name))
	{
		// allow alphanumeric, space, -^.,()[]{}*+?_!=
		return to_alphanumeric_extended(get_unsafe_variable($var_name));
	}
	else
	{
		return "";
	}
}


function save_as_cookie($var_name)
{
	save_as_renamed_cookie($var_name, $var_name);
}


function save_as_renamed_cookie($old_var_name, $new_var_name)
{
	if (is_variable_set($old_var_name))
	{
		$value = get_unsafe_variable($old_var_name);
		setcookie($new_var_name, $value, time() + 31536000, "/");
		$_COOKIE[$new_var_name] = $value;
	}
}


function delete_all_cookies()
{
	$past = time() - 3600;
	foreach ($_COOKIE as $key => $value)
	{
		setcookie($key, "", $past, "/");
		unset($_COOKIE[$key]);
	}
}


function getPassword()
{
	return sanitised_as_alphanumeric_extended("password");
}


function getClaimedNickname()
{
	return sanitised_as_alphanumeric_extended("nickname");
}


function getNickname()
{
	$claimed_nickname = getClaimedNickname();

	if (get_user($claimed_nickname) == null)
	{
		return "";
	}

	return $claimed_nickname;
}


function edittingNickname()
{
	return is_variable_set("edit_nickname");
}


function isAppInstalled()
{
	return is_variable_set("app_installed");
}


