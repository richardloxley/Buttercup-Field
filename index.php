<?php

include_once("template.inc.php");
include_once("password.inc.php");
include_once("mobile.inc.php");
include_once("post-variables.inc.php");
include_once("database.inc.php");

// do this first as we need to set cookies before outputting headers
handlePostVariables();

draw_header();

if (gotPassword())
{
	draw_nickname_form();
	draw_blackboard();
	draw_video_chat();
}

draw_footer();
