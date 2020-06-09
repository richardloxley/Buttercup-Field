<?php

include_once("../include/template.inc.php");
include_once("../include/password.inc.php");
include_once("../include/text-chat.inc.php");

goHomeOnWrongPassword();

draw_header("Livestream party", "single-module-whole-page");
draw_text_chat(1, "Livestream chat", true);
draw_footer();
