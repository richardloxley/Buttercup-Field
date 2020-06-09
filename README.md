# Buttercup Field
Supports an online "festival" including video chat rooms and event announcements.

_(Named after our favourite juggling festival which is held in a buttercup field.)_

## Requirements
Requires: a webserver, PHP, MySQL (or similar), use of a third-party video conferencing system (currently [Jitsi Meet](https://meet.jit.si/)).

Users of the site require a web browser that supports Jitsi, or the Jitsi mobile app.  JavaScript is required for some features.

## Licence
Code licence: GNU GPLv3.  The directory "third_party" contains example images and fonts created by third parties which are made available under separate licences.  I believe that the licences would cover most typical installations of the software.

## Installation
Install the files to the root of a domain, import "database.schema.sql" into your database, copy "config.inc.example.php" to "config.inc.php", and edit it appropriately.  Make sure the directories "video_thumbs" and "uploads" have write permission by the web server.

## Known limitations

- the blackboards don't yet update asynchronously if someone edits them (you need to refresh the page)
- when a user edits something, it uses a form submission; if they then refresh the page the form will be submitted again
- deleting a video room doesn't delete its thumbnail image from the "video_thumbs" directory
- the blackboards should have the ability to "reply" to an entry; this isn't finished and is therefore commented out in the code
- the URL parsing in the text chat window is overzealous

## Feature wishlist

- allow video room names to have non-alphanumeric characters
- ability for the user to choose which blackboard font they use
- link from video rooms back to the main site
- ability for people to edit video room descriptions/thumbnails without deleting and re-creating
- sounds on receipt of a text chat message - partial experimental support is written which is turned on for the (commented out) special event chat
- show which user wrote/edited a blackboard entry
- allow all modules to be opened in separate windows (like the text chat box)
- maybe allow images to be posted to the text chat?
