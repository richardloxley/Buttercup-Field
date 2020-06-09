<?php

$DEBUG = false;

$SITE_TITLE = "The Festival";
$SITE_GREETING = "Welcome to the festival!";

// NB. just need this password to be anywhere in the entered password!
$PASSWORD = "saucer";
$PASSWORD_HINT = "(Hint: what would you put your cup on?)";

$CONTACT_US_HTML = "If you need help, please send us a message on <a href='https://twitter.com/example'>Twitter</a> or <a href='https://www.facebook.com/example/'>Facebook</a> or <a href='http://www.example.com/contact/'>our website.</a>";

$MAIN_TEXT_CHAT_TITLE = "Big Top chat";

$TEXT_CHAT_RETENTION_IN_HOURS = 12;

$JITSI_UNIQUE_ROOM_PREFIX = "ChangeThisToMakeYourChatRoomsUnique";

$JITSI_MOBILE_APP_URL_IOS = "https://itunes.apple.com/us/app/jitsi-meet/id1165103905";
$JITSI_MOBILE_APP_URL_ANDROID = "https://play.google.com/store/apps/details?id=org.jitsi.meet";

// limiting the framerate of video chats can allow older computers to join in
$JITSI_FRAMERATE_IDEAL = 15;
$JITSI_FRAMERATE_MAX = 30;

// you may need to change permissions on these directories to allow the web server to write to them
$VIDEO_ROOM_THUMBNAIL_UPLOAD_DIR = "video_thumbs";
$IMAGE_UPLOAD_DIR = "uploads";

$DB_SERVERNAME = "localhost";
$DB_DATABASE = "festival";
$DB_USERNAME = "festival";
$DB_PASSWORD = "Password1:only-kidding!";
