=== MZ Mindbody API ===
Contributors: mikeill, rtzee
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A95ZEELLHGECE
Tags: mindbody, schedule, calendar, yoga, MBO, mindbodyonline, gym
Requires at least: 3.0.1
Tested up to: 4.7.2
Stable tag: 2.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MZ Mindbody API uses the Devin Crossman API to interface with MindBodyOnline and display Staff, Schedule and Workshops.

This free plugin also enables users to register with MBO within your site, as well as signing up for classes.


== Description ==

MZ Mindbody API uses the Devin Crossman API to interface with MindBodyOnline and display Staff, Schedule and Workshops.

Two important requirements are:

    1. PEAR and SOAP must be installed/enabled on the web server
    2. MindBodyOnline API Developer Account

(NOW REQUIRES PHP 5.6 or GREATER)

Open Source on [GitHub](https://github.com/MikeiLL/mz-mindbody-api)

== Installation ==

Steps to install and configure MZ Mindbody API:

1. Upload the directory, `mz_mindbody_api` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Follow instructions at [mZoo.org](http://www.mzoo.org/creating-your-mindbody-credentials).
4. In the WP Admin panel go to: Settings -> MZ Mindbody and configure settings
5. This plugin includes some minimal CSS which you can override in theme.
6. Also includes some bootstrap css and javascript so check there if conflicts arise.
7. Read through the Settings > MZ Mindbody page for further instructions and options.

== Frequently Asked Questions ==

= One of the elements I need to fill is are the *Event IDs*. Can you help me learn where to find those? =

I'm not sure if there's an easier way, but you can find them by, within MindBody,
going to an Event EDIT page and viewing the source of the Dropdown menu items, which
contain the name of each event type and it's associated ID number.

== Why am I getting "Permission denied" and "Invalid Argument" errors? ==

You need to register a developer account with MindBody, which costs $5+ per website. Follow the instructions at [mZoo.org](http://www.mzoo.org/creating-your-mindbody-credentials).

== Screenshots ==

1. Calendar Display
2. Details Modal
3. Teacher Bio Page
4. Admin Page

== Changelog ==

= 2.5.1 =
Bugfix: url for class sign-up via MBO
Bugfix: add default checkbox values for a couple of admin settings

= 2.5.0 =
Fix/reinstate single day schedule display 

= 2.4.9 =
Fix problem with deactivation method.
Fix issue with specifying locations in Events display
Fix issue with incorrect "default" listed in Admin page copy.
Add ability to filter events listing by location.

= 2.4.8 =
Fix path for Template override directory

= 2.4.7 =
Completely refactor the plugin with a more informed approach
Implement Gemajo templating class so users can overwrite shortcode templates
Implement Eric Mann's WordPress session managment
Allow users to login and out, create accounts within same page as schedule/events
Separate plugin codebase from Docker dev env wrapper
Add try/except wrapper around `SoapClient`.
Add try/except wrapper around `require Server.php`.
Add unit test scaffolding
Add Autoload and namespacing
Add option to log MBO API calls

= 2.4.6 =
Add docker wrapper and phpunit test scaffolding.
Bug fix: missing (not yet used) dependency.

= 2.4.5 =
Bug Fix: Replace Global that wasn't available.

= 2.4.4 =
Fix error where only single week of events displayed.

= 2.4.2 =
Fix break with events listing that occurred in last release.
Fix break in signup links also from recent updates.

= 2.4.1 =
Show full seven days in horizontal mode starting current day.
Remove date filter from javascript to php.

= 2.4.0 =
Remove Modernizr.
Add shortcode to display limited number of events.

= 2.3.9 =
Fix typo and add class.

= 2.3.8 =
Fix issue with events duration length display and enable modal window on events list.

= 2.3.7 =
* New parameter for Events - "list=1" to display short list of events

= 2.3.6 =
* Fix error with show_registrants which got broken in development

= 2.3.5 =
* Fix bug with multiple locations requiring space between them in shortcode.
* Use Object for Class Event to reduce code redundancy. (Dry it up)
* Fix errors with transients

= 2.3.4 =
* Fix error date_display not displaying with add to class.

= 2.3.3 =
* Wrap $ calls in (`function($) { //$ here })( jQuery );`

= 2.3.2 =
* Display schedule navigation even if there are no classes in current week.
* Allow shortcode argument to hide cancelled classes from calendar: show_cancelled=1
* Replace `$(document).ready(function($)` with `jQuery(document).ready(function($)` 

= 2.3.1 =
* Use OOP to create link for schedule pop-up.

= 2.3.0 =
* Repair cache reset.

= 2.2.9 =
* Build transient name dynamically based on shortcode atts and $_GET variable.

= 2.2.8 =
* Fix error with timestamp in schedule.

= 2.2.7 =
* Refactor to make Schedule script more DRY.
* Replace Bootstrap Modal with colorbox which seems to be less likely to cause theme conflicts.
* Fix broken caching.

= 2.2.6 =
* Not much of note

= 2.2.5 =
* Clean up and add a little output on SOAP ERROR.

= 2.2.4 =
* Remove jquery version 2 so we stop breaking themes
* Add shortcode option to display registrants for classes

= 2.2.3 =
* I forget

= 2.2.2 =
* Using bootstrap gallery to optionally display staff page as gallery
* Hopefully adding the new CSS won't cause theme conflicts.

= 2.2.0 =
* Refactor with more OOP approach
* Could be further refactored.
* Fix error with events display

= 2.1.0 =
* Can display multiple locations on same calendar with select to filter. Shortcode
* to show calendar with specified class types.
* Using modified version of jQuery.filterTable now.
* Locations now held in array, but still supporting older single item method.
* Add CSS classes to horizontal schedule display cells.

= 2.0.0 =
This release includes the most new features and also uses a more minimal version of bootstrap code:
just the necessary components. New jQuery filter, grid mode schedule view, configurable event cycle duration,
multiple accounts and locations can be shown also via shortcode attributes.
Internationalized - so far Spanish and Portuguese.

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

= 2.4.9 =
Events listing can now have an added Location Filter.

= 2.4.7 =
MZ Mindbody API has been almost completely re-written. Requires php version >= 5.6. You _may_ have to re-enter some settings.

= 2.4.5 =
Bug fix.

= 2.4.4 =
No new features, just some bug fixes.

= 2.4.1 =
Horizontal schedule shows next seven days from current day.
Also ability (beta) to show details about "class owner" for subbed classes.

= 2.3.8 =
Now you can display Events (Enrollments) as a list with Modal Popup showing details.

= 2.3.7 =
* New parameter for Events - "list=1" to display short list of events

= 2.3.6 =

= 2.3.5 =
Allow special Characters in Event/Class Titles
Add multiple account support in events
Enable toggle between horizontal and grid calendar display

= 2.2.7 =
Wider Theme compatibility for Modal Pop-ups.
Can display registrants on class pop-ups.

= 2.2.2 =
Add gallery mode option for Staff page.

= 2.2.0 =
Refactoring update.

= 2.1.0 =
Display multiple locations on same calendar with SELECT to filter. New shortcode
to show calendar with specified class types.

= 2.0.0 =
New jQuery filter, grid mode schedule view, configurable event cycle duration,
multiple accounts and locations can be shown also via shortcode attributes.
Internationalized - so far Spanish and Portuguese.

= 1.6 =
Add Today's Schedule widget - must be only MBO call on page!

= 1.1 =
Now compatible with php versions less than 5.3

== Notes ==

This is not a terribly easy plugin to install. 
Configuring the server may require input from your web hosting company (or you might get lucky).
And getting all the MindBody details configured can also be a little laborious.
Also mindbody is charging developers $5 per month per account connection now.


