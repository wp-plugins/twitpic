=== TwitPic ===
Contributors: grobekelle
Donate link: http://www.grobekelle.de/donate
Tags: twitpics, wordpress, twitter, tweets, twit, follower, friends, badge, widget, widgets, sidebar, admin, plugin, images, blogging, microblogging, twittpics, twitpic, twittpics, pictures, gallery, pics
Requires at least: 2.0.2
Tested up to: 2.7
Stable tag: 0.2

Displays your latest pictures from TwitPic in the sidebar of your blog. The plugin is widget ready and comes with many configuration options!

== Description ==

Displays your latest pictures from TwitPic in the sidebar of your blog. The plugin is widget ready and comes with many configuration options! Because every theme brings their very own style, TwitPic has very little preset css. Please see the css file `twitpic.css` to set collors, borders etc.

Check out more [Wordpress Plugins](http://www.grobekelle.de/wordpress-plugins "Wordpress Plugins") and [Lustige Videos](http://www.grobekelle.de "Lustige Videos") brought to you by Grobekelle.

== Installation ==

1. Unpack the zipfile twitpic-X.y.zip
1. Upload folder `twitpic` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to settings -> TwitPic and type in your username etc.
1. Make sure that the `cache` directory inside the plugin folder is writeable
1. Place `<?php if(function_exists('twitpic_display')) twitpic_display(); ?>` in your template or use the sidebar widgets.

== Frequently Asked Questions ==

= Does this plugin comes with a "follow me" button? =

Yes it does. Please go to settings -> TwitPic to enable/disable this feature

= Does this plugin caches the images? =

Yes, a Tweet gets cached for 30 minutes!

== Screenshots ==

1. TwitPic injected to the blogs sidebar via widget
1. admin panel

== Change Log ==

* v0.2 21.09.2009 added content filter to convert links to twitpic to embedded images
* v0.1 14.07.2009 initial release

