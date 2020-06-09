<?php

include_once("../include/template.inc.php");
include_once("../include/password.inc.php");
include_once("../include/text-chat.inc.php");

goHomeOnWrongPassword();

draw_header($MAIN_TEXT_CHAT_TITLE, "single-module-whole-page");
draw_text_chat(0, $MAIN_TEXT_CHAT_TITLE, false);
draw_footer();
