<?php

$DEBUG = false;

$SITE_TITLE = "The Festival";
$SITE_GREETING = "Welcome to the festival!";

// NB. just need this password to be anywhere in the entered password!
$PASSWORD = getenv('PASSWORD');
$PASSWORD_HINT = getenv('PASSWORD_HINT');

$CONTACT_US_URL = "https://www.example.com/contact/";

$JITSI_UNIQUE_ROOM_PREFIX = "ChangeThisToMakeYourChatRoomsUnique";

$ROOM_DEFAULT_IMAGE = "/images/bigtop.jpg";

$DB_SERVERNAME = getenv('DB_SERVERNAME');
$DB_DATABASE = getenv('DB_DATABASE');
$DB_USERNAME = getenv('DB_USERNAME');
$DB_PASSWORD = getenv('DB_PASSWORD');
