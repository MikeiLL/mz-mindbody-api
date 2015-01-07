=== MZ Mindbody API ===
Contributors: mikeill, rtzee
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A95ZEELLHGECE
Tags: mindbody, schedule, calendar, yoga, soap, pear
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: 1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MZ Mindbody API uses the Devin Crossman API to interface with MindBodyOnline and display Staff, Schedule and Workshops.


== Description ==

MZ Mindbody API uses the Devin Crossman API to interface with MindBodyOnline and display Staff, Schedule and Workshops.

Two important requirements are:

    1. PEAR and SOAP must be installed/enabled on the web server
    2. MindBodyOnline API Developer Account

(PHP 5.2 compliant.)

== Installation ==

Steps to install and configure MZ Mindbody API:

1. Upload the directory, `mz_mindbody_api` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Follow instructions at [mZoo.org](http://www.mzoo.org/creating-your-mindbody-credentials).
4. In the WP Admin panel go to: Settings -> MZ Mindbody and configure settings
5. It will probably be necessary to add .css either in child theme or elsewhere:

    `.modal-sm {
    background-color: #fff;
    }`

== Frequently Asked Questions ==

= Seriously, have you ever made a plugin before? =

This is the first released one, so please be gentle.

= One of the elements I need to fill is are the *Event IDs*. Can you help me learn where to find those? =

I'm not sure if there's an easier way, but you can find them by, within MindBody,
going to and Event EDIT page and viewing the source of the Dropdown menu items, which
contain the name of each event type and it's associated ID number.

== Why am I getting "Permission denied" and "Invalid Argument" errors? ==

You need to register a developer account with MindBody, which costs $5+ per website. Follow the instructions at [mZoo.org](http://www.mzoo.org/creating-your-mindbody-credentials).

== Screenshots ==

1. Calendar Display
2. Details Modal
3. Teacher Bio Page
4. Admin Page

== Changelog ==

= 1.6 =
Fix some HTML validation errors in schedule table layout. 

= 1.5 =
(Re-)integration of short code type=day for schedule page to show “today’s” classes.

= 1.4 =
Further navigation refactoring.

= 1.3 =
Refactor navigation and address conflict with older version of MB_API.

= 1.2 =
Fix navigation on Schedule page when Force Cache not selected.

= 1.0 =
Initial release.

== Upgrade Notice ==

= 1.6 =
Add Today's Schedule widget - must be only MBO call on page!

= 1.1 =
Now compatible with php versions less than 5.3

== Notes ==

This is not a terribly easy plugin to install. 
Configuring the server may require input from your web hosting company (or you might get lucky).
And getting all the MindBody details configured can also be a little laborious.
Also mindbody is charging developers $5 per month per account connection now.


