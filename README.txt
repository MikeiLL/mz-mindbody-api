=== MZ Mindbody API ===
Contributors: mikeill, rtzee, sunspotswifi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A95ZEELLHGECE
Tags: mindbody, schedule, MBO, mindbodyonline, gym.
Requires at least: 5.3
Tested up to: 6.5.2
Stable tag: 2.10.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display special events, class schedules and instructors from Mindbody.

Light weight, fast.

Configurable with template over-rides.

== Description ==

Display special events, class schedules and instructors from Mindbody.

Configurable with template over-rides in your theme.

Easy to extend, includes feature for displaying registrants.

Schedule display can be vertical or horizontal. User can switch between both.

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

= One of the elements I need to fill out are the *Event IDs*. Can you help me learn where to find those? =

I'm not sure if there's an easier way, but you can find them by, within MindBody,
going to an Event EDIT page and viewing the source of the Dropdown menu items, which
contain the name of each event type and it's associated ID number.

== Why am I getting "Permission denied" and "Invalid Argument" errors? ==

You need to register a developer account with MindBody, which costs $11+ per website. Follow the instructions at [mZoo.org](http://www.mzoo.org/creating-your-mindbody-credentials).

== Screenshots ==

1. Horizontal Calendar Display
2. Grid Calendar Display
3. Details Modal
3. Staff Page Gallery
4. Staff Page Full
5. Admin Page

== Changelog ==

= v2.10.6 =
Support specified schedule length in instance of RetrieveClasses.

= v2.10.5 =
Abreviate short description and limit to five tags.

= v2.10.4 =
Fix bug in get_registrants.
Migrate some css to child plugin.

= v2.10.3 =
Tested up to 6.5.2
bugfix: missing classId in retrieve registrants
Clean out some old code.
Remove some logging.

= v2.10.2 =
bugfix: show registrants hadn't been working because data-classid was missing.
some refactoring of MBO library.
replace some implementations of ajax with wp_send_json methods.

= v2.10.1 =
Fix: dumb, wrong syntax for casting string to int.

= v2.10.0 =
Log and return error string if MBO token request response code > 299.

= v2.9.9 =
Migrate Session management from MBO Access plugin.
Add support for Consumer API and Oauth via extension plugin.

= v2.9.8 =
bugfix: Correct dates in horizontal_display schedule.
enhancement: log api_calls, if configured to, in weekly increments, named by week date.

= v2.9.7 =
bugfix: Ensure AccessToken key exists; avoid notices if Access plugin being enabled without MBO credentials.
bugfix: Esc_Html on Staff Bio on Events display.

= v2.9.6 =
Remove broken pre-update admin message feature.

= v2.9.5 =
Reduce API calls when the Memberships and Contracts are not defined.

= v2.9.4 =
Wrap plugin update message fetch readme.txt in try catch in case unable to access.

= v2.9.3 =
Add limited wp-cli support: clear transients, fetch new api key, reset admin api excess alerts.

= v2.9.2 =
Create transients for empty Site and Sale endpoints so not continuing to ping API looking.

= v2.9.1 =
Refactor admin email on api call excess, set cron job.
Subclass v5 and v6 APIs to share some methods.
Add admin button to remove email api access excess alerts.

= v2.9.0 =
Improve error message when too-many api calls exception bubbles up.
Update signup link to match new Mindbody url.
Update event registration link to match new Mindbody url.

= v2.8.9 =
Improve token check to prevent error.
Set default log file path in plugin upgrade check.
Move deactivate_plugins to admin_init action to insure the function is loaded.
No location id in queries.

= v2.8.8 =
Refactor use of hooks on activation, deactivation, initialization.

= v2.8.7 =
Bugfix: Clean out old files from subversion repo.

= v2.8.6 =
Bugfix: initialize event_id selection to quiet php warning.
Bugfix: coerce is_substitute to int so strict comparison works.

= v2.8.5 =
Bugfix: Correct misuse of wp_date.
Implement more WP Standards.
Replace my get_blog_timezone() with native wp_timezone().
HTMLEntity decode staff biography.

= v2.8.4 =
Bugfix: Correct path to Frontend view Templates.
Bugfix: Update uses of date and wp_date methods.
Bugfix: Events locations corced to int to strict check.
Release method update, using composer install --no-dev.

= v2.8.3 =
Bugfix: error with modal popup on Staff gallery.
House Cleaning: Wordpress standards updates, docblocks.

= v2.8.2 =
Bugfix: calls to check_ajax_referrer
Bugfix: check_ajax_referrer to check_admin_refer where admin.

= v2.8.1 =
Refactor to meet PSR12 standard recommendations.
Replace date_i18n with wp_date.
Replace rarst wordpress datetime with new WP native functions.
Allow calls to check_ajax_referrer to die on fail.

= v2.8.0 =
Bugfix: Catch error thrown when Too Many API Calls level reached.

= v2.7.9 =
Notify admin of too many API calls within add_action plugins_loaded hook.

= v2.7.8 =
Just in case faulty deploy not fixed previously.

= v2.7.7 =
Fix faulty deploy.

= v2.7.6 =
Bugfix: Remove test print.
Refactor token management, moving storage out of MBO api class.

= v2.7.5 =
Bugfix: Correct endpoint for Site requests.
Add authorization header for some endpoints.

= v2.7.4 =
Support Authorized MBO transactions.

= v2.7.3 =
Add Bootstrap modal css.

= v2.7.2 =
Update admin api test script, removing a call to log function.

= v2.7.1 =
Debug: Fix another coding error in class-activator.

= v2.7.0 =
Debug: Fix coding error in class-activator.

= v2.6.9 =
Enhancement: Change button text to "waitlist" for waitlist only registrations.

= v2.6.8 =
Enhancement: Allow specification of SessionTypeIds in shortcode to specify retrieval data

= v2.6.7 =
Bugfix: Just passing token string, as opposed to object so don't try to get property.
Enhancement: Disable signup button when class is at capacity and no waitlist available.
Bugfix: jQuery Staff popup conflict resolved.
Enhancement: Allow admin to set schedule transient duration.

= v2.6.6 =
Bugfix: Cancelled classes were being hidden even when not configured to be hidden.

= v2.6.5 =
Remove Pad empty calendar days with blank rows until bugs worked out.

= v2.6.4 =
Pad empty calendar days with blank rows.
Bugfix: Fix transient usage. Had been calling API every time.
Bugfix: Stop importing sign-up script that was calling a non-existent method.
Bugfix: Alphanumeric validity check was targeting object rather than string.

= v2.6.3 =
Bugfix: get_and_save_staff_token from private to public to hook accessible.
Update npm dependencies as per DependaBot.

= v2.6.2 =
Enhancement: Support override for transient in get_mbo_results.

= v2.6.1 =
Bugfix: Class method name had changed in one place, but not in another.

= v2.6.0 =
Add a helper function.

= v2.5.9 =
Bugfix: fix session_type shortcode attribute.
Bugfix: some debug logging
Remove Sessions and Client which are now in a child plugin.

= v2.5.8 =
Bugfix: fix php missing call to MZMBO to specify variable as class property.
Bugfix: Reinstate siteID used in signup links in class schedules.
Bugfix: Fix way cancelled classes handled.
Increase limit of items pulled from MBO from 100 to 200 (the max).

= v2.5.7 =
Utilize v6 of the MBO API with v5 left for legacy purposes.
Updated Credentials will be required.
Minimum php version of 7.0.
Swap out "loading" element with Bootstrap load.
Upgrade to Bootstrap v4.
Manage assets with Webpack instead of Bower.
Add support for MBO Access plugin.

= v2.5.6 =
Alert admin when API calls get close to 1000 and short circuit calls when past 2000.

= v2.5.5 =
Bugfix: fix php error displaying on schedule when MBO credentials bad.

= v2.5.4 =
Bugfix: fix event registration URL.

= v2.5.3 =
Bugfix: some registrations were pulling user data from previous class registration.

= v2.5.2 =
Bugfix: reinstate default timestamp in retrieve classes time_frame method.

= v2.5.1 =
Bugfix: url for class sign-up via MBO
Bugfix: add default checkbox values for a couple of admin settings

= v2.5.0 =
Fix/reinstate single day schedule display

= v2.4.9 =
Fix problem with deactivation method.
Fix issue with specifying locations in Events display
Fix issue with incorrect "default" listed in Admin page copy.
Add ability to filter events listing by location.

= v2.4.8 =
Fix path for Template override directory

= v2.4.7 =
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

= v2.4.6 =
Add docker wrapper and phpunit test scaffolding.
Bug fix: missing (not yet used) dependency.

= v2.4.5 =
Bug Fix: Replace Global that wasn't available.

= v2.4.4 =
Fix error where only single week of events displayed.

= v2.4.2 =
Fix break with events listing that occurred in last release.
Fix break in signup links also from recent updates.

= v2.4.1 =
Show full seven days in horizontal mode starting current day.
Remove date filter from javascript to php.

= v2.4.0 =
Remove Modernizr.
Add shortcode to display limited number of events.

= v2.3.9 =
Fix typo and add class.

= v2.3.8 =
Fix issue with events duration length display and enable modal window on events list.

= v2.3.7 =
* New parameter for Events - "list=1" to display short list of events

= v2.3.6 =
* Fix error with show_registrants which got broken in development

= v2.3.5 =
* Fix bug with multiple locations requiring space between them in shortcode.
* Use Object for Class Event to reduce code redundancy. (Dry it up)
* Fix errors with transients

= v2.3.4 =
* Fix error date_display not displaying with add to class.

= v2.3.3 =
* Wrap $ calls in (`function($) { //$ here })( jQuery );`

= v2.3.2 =
* Display schedule navigation even if there are no classes in current week.
* Allow shortcode argument to hide cancelled classes from calendar: show_cancelled=1
* Replace `$(document).ready(function($)` with `jQuery(document).ready(function($)`

= v2.3.1 =
* Use OOP to create link for schedule pop-up.

= v2.3.0 =
* Repair cache reset.

= v2.2.9 =
* Build transient name dynamically based on shortcode atts and $_GET variable.

= v2.2.8 =
* Fix error with timestamp in schedule.

= v2.2.7 =
* Refactor to make Schedule script more DRY.
* Replace Bootstrap Modal with colorbox which seems to be less likely to cause theme conflicts.
* Fix broken caching.

= v2.2.6 =
* Not much of note

= v2.2.5 =
* Clean up and add a little output on SOAP ERROR.

= v2.2.4 =
* Remove jquery version 2 so we stop breaking themes
* Add shortcode option to display registrants for classes

= v2.2.3 =
* I forget

= v2.2.2 =
* Using bootstrap gallery to optionally display staff page as gallery
* Hopefully adding the new CSS won't cause theme conflicts.

= v2.2.0 =
* Refactor with more OOP approach
* Could be further refactored.
* Fix error with events display

= v2.1.0 =
* Can display multiple locations on same calendar with select to filter. Shortcode
* to show calendar with specified class types.
* Using modified version of jQuery.filterTable now.
* Locations now held in array, but still supporting older single item method.
* Add CSS classes to horizontal schedule display cells.

= v2.0.0 =
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

= v2.5.7 =
Now requires php 7.1 or greater.
Now using MBO v6 API. You will want to get your updated MBO v6 credentials ready before upgrading.

= v2.4.9 =
Events listing can now have an added Location Filter.

= v2.4.7 =
MZ Mindbody API has been almost completely re-written. Requires php version >= 5.6. You _may_ have to re-enter some settings.

= v2.4.5 =
Bug fix.

= v2.4.4 =
No new features, just some bug fixes.

= v2.4.1 =
Horizontal schedule shows next seven days from current day.
Also ability (beta) to show details about "class owner" for subbed classes.

= v2.3.8 =
Now you can display Events (Enrollments) as a list with Modal Popup showing details.

= v2.3.7 =
* New parameter for Events - "list=1" to display short list of events

= v2.3.6 =

= v2.3.5 =
Allow special Characters in Event/Class Titles
Add multiple account support in events
Enable toggle between horizontal and grid calendar display

= v2.2.7 =
Wider Theme compatibility for Modal Pop-ups.
Can display registrants on class pop-ups.

= v2.2.2 =
Add gallery mode option for Staff page.

= v2.2.0 =
Refactoring update.

= v2.1.0 =
Display multiple locations on same calendar with SELECT to filter. New shortcode
to show calendar with specified class types.

= v2.0.0 =
New jQuery filter, grid mode schedule view, configurable event cycle duration,
multiple accounts and locations can be shown also via shortcode attributes.
Internationalized - so far Spanish and Portuguese.

= 1.6 =
Add Today's Schedule widget - must be only MBO call on page!

= 1.1 =
Now compatible with php versions less than 5.3

== Notes ==

Now that we're using Mindbody's v6 api, this should be pretty easy to get going.
