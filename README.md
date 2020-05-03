# Buttercup Field
Supports an online "festival" including video chat rooms and event announcements.

_(Named after our favourite juggling festival which is held in a buttercup field.)_

Requires: a webserver, PHP, MySQL (or similar), use of a third-party video conferencing system (currently [Jitsi Meet](https://meet.jit.si/)).

Users of the site require a web browser that supports Jitsi, or the Jitsi mobile app.  JavaScript is beneficial but probably not required.

Install the files to the root of a domain, import "database.schema.sql" into your database, copy "config.inc.example.php" to "config.inc.php", and edit it appropriately.

If it's not running in the root of the domain, you might need to check for any absolute URL paths I've left in!

This is currently a work in progress:

- the video chat room list has to be populated manually in the database
- the list of occupants of the chat rooms doesn't yet update correctly if people join using a mobile app
- the list of occupants of the chat rooms doesn't yet update asynchronously (you need to refresh the page)
- the 'blackboard' announcement module isn't yet written
