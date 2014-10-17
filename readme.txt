=== Custom Login ===
Contributors: austyfrosty
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7431290
Tags: admin, branding, customization, custom login, login, logo, error, login error, custom login pro
Requires at least: 3.5
Tested up to: 4.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use this plugin to customize your login screen, great for client sites!

== Description ==

**Version 2.0** is now 140% faster in the admin and uses a lot less resources! Minimum WordPress version 3.5.

Join in on the [conversation](http://austin.passy.co/wordpress-plugins/custom-login) on my personal blog.

You can find Custom Login [PRO](https://extendd.com/plugin/custom-login-pro/)  on [Extendd.com](https://extendd.com): A plugin marketplace. New features include faster login loading (no database access), Custom Post Types (for multiple designs) and four default CSS designs. **Custom Login Pro is a completly different plugin than Custom Login**.

http://www.youtube.com/watch?v=XOZwaLwpjNo

= Extensions =

Custom Login 2.0 now has many extensions to make your login page better!

**Available Now**

* *In Custom Login > version 2.2 you can auto-install all extensions within the settings page with an active licence key.*
* <a href="https://extendd.com/plugin/custom-login-stealth-login?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=freemium" title="Custom Login Stealth Login">Stealth Login</a> - obscure your login URL.
* <a href="https://extendd.com/plugin/custom-login-page-template?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=freemium" title="Custom Login Page Template">Page Template</a> - add a login form to any WordPress page. 
* **NEW** <a href="https://extendd.com/plugin/wordpress-login-redirects?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=freemium" title="Custom Login Redirects">Login Redirects</a> - Manage login redirects. 

**In Development**

* User Hash Key logins (think generated guest logins with-out having to use the login form).
* Email Logins for usernames.
* 2-step Authentication.
* "Super User" only access for client sites.
* WordPress Login CSS style remover (for sites that see a quick flash of the default login page).
* Submit button styles!
* Custom Login templates.

= More info =

Activate the plugin and customize your WordPress login screen. Use the built-in and easy to use settings page to do the work for you. There's no need to understand CSS at all!
Now featureing a HTML, CSS &amp; jQuery box for advanced users to up the customization!

1. Works great for client site installs.
2. Read more about [Custom Login](http://wp.me/pzgsJ-HY) 2.0

**For those looking to showoff your login screen, check out the [Flickr group](http://flickr.com/groups/custom-login/)! Share you designs with the community!**

= links =

* Plugin Marketplace: [Extendd.com](https://extendd.com/ "WordPress plugin markeetplace")
* My Blog: [http:/austin.passy.co/](http://austin.passy.co/ "Austin Passy's blog")
* Follow me on Twitter: @[TheFrosty](https:/twitter.com/TheFrosty "Austin TheFrosty' Passy on Twitter")
* Follow Extendd Twitter: @[Extendd](https:/twitter.com/WPExtendd "Extendd on Twitter")
* **Contribute on [GitHub](https://github.com/thefrosty/custom-login)**

= Hooks and Filters =

You can build your own extensions.

== Installation ==

Follow the steps below to install the plugin.

1. Upload the `custom-login` directory to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings/custom-login to edit your settings.
4. Design away.

== Frequently Asked Questions ==

= What are extensions? =
They are additional plugins that add functionality to the Custom Login plugin. Depending of what the extension does. You can find all Custom Login extensions on [extendd.com](https://extendd.com).

= My new settings aren't showing up =
This plugin caches all settings in a transient, try clicking the new 'Update stylesheet' button to delete and refresh the cache.

= Is there a PRO version? =
Why yes there is, you can purchase the [PRO](https://extendd.com/plugin/custom-login-pro/) version on [https://extendd.com](https://extendd.com). Note that since Custom Login 2.0, Custom Login PRO is a completely different plugin.

= Why create this plugin? =
I created this plugin to allow for custom login of any WordPress login screen. See working example on: [Extendd.com](https://extendd.com/wp-login.php?action=login).

= Where can I upload and share my cool login screen? =
Check out the newly created [Flickr group](http://flickr.com/groups/custom-login/)! Upload and add it to our pool!

= I think i want to uninstall =
Just de-active.

== Screenshots ==

Screenshots of working example in our [Flickr group](http://flickr.com/groups/custom-login/)

1. Custom Login Settings page (as of v 2.0).

2. Example of a custom login page using the nyan.cat as a animated background! [see user generated designs](http://flickr.com/groups/custom-login/).

== Changelog ==

= Version 2.3.8 (10/17/14) =

* Updated admin.js bug.
* Update settings page.
* Working on extensions auto install fix...

= Version 2.3.7 (7/7/14) =

* Fixes CSS issue on lost password page. Issue [<a href="https://github.com/thefrosty/custom-login/issues/1#issue-37305001">#1</a>].

= Version 2.3.6 (6/23/14) =

* Update Chosen.js to 1.1.0
* Update admin.css 
* Fix opacity select options from being hidden when opacity is checked (on).

= Version 2.3.5 (6/18/14) =

* Update all extendd URLs to https.

= Version 2.3.4 (6/3/14) =

* Added `do_action( 'custom_login_admin_enqueue_scripts' )` for extenstions to hook into settings page `admin_enqueue_scripts`.

= Version 2.3.3 (5/14/14) =

* Attempt to fix `Fatal error: Call to a member function set_sections() on a non-object`.

= Version 2.3.2 (4/24/14) =

* Fixed Fatal Error. Sorry.

= Version 2.3.1 (4/24/14) =

* Added new extension to manage redirects.
* Upate the Welcome page.

= Version 2.3 (3/13/14) =

* Renamed helper functions to avoid function exists errors even though I've wrapped those functions in `function_exists`
* New: Class 'Custom_Login_Scripts_Styles'.

= Version 2.2.3 (1/28/14) =

* Added image previews after file fields.

= Version 2.2.2 (1/28/14) =

* Allowed variables to account for unicode in Custom CSS.
* - Use `%%BLASH%%` for a backslash.

= Version 2.2.1 (1/25/14) =

* $version variable not loading.
* Updated sanitization on Custom CSS & HTML textarea fields.
* CSS sanitized with `wp_filter_nohtml_kses`.
* HTML sanitized with `wp_kses_post`.
* Allow use of "Tab" key in the CSS textarea to format CSS.

= Version 2.2.0 (1/24/14) =

* Updated settings page and CSS (now responsive).
* Updated Genericons to v 3.0.3.
* Add Remote Install class to auto-install Custom Login Extensions from Extendd.com (license key needed for paid extensions).
* Update and cleanup code.

= Version 2.1.9 (1/8/14) =

* Fixed chosen() JS 1.0.0 class variables on opacity options.
* Be sure to only output one instace of the settings scripts.
* Added actual width and height options to logo to fix WP 3.8 CSS settings.

= Version 2.1.8 (12/17/13) =

* Logo CSS fix.
* Updated Chosen.js to `1.0.0`

= Version 2.1.7 (12/13/13) =

* WordPress 3.8.x compatability. 
* Adds custom background-size capability to fix new `80px X 80px` background-size standard.

= Version 2.1.6 (11/21/13) =

* Updated for PHP 5.4 &amp; WordPress 3.8.x compatibility. 

= Version 2.1.5 (5/20/13) =

* Fix for some users who still can't dismess the notice.

= Version 2.1.4 (5/17/13) =

* Fixed notification not clearing on 'dismiss notice'. Sorry about that.

= Version 2.1.3 (5/13/13) =

* Fixed `notice` in class.settings-api.php.

= Version 2.1.2 (4/22/13) =

* Changed wrong $hook prefix for scripts.

= Version 2.1.1 (4/22/13) =

* Fix untranslatable items in the settings page.
* Added clear transient cache to Custom Login sidebar (as well as above save settings).
* Changed HTML position from input field to dropdown.
* Added notification on transient cache deletion.
* Removed "test" echo.
* Added delete transient on settings save.

= Version 2.1.0 (4/18/13) =

* Updated notice URL.
* For reals fixed the "remove notice" button. :D
* Moved the CSS back into the login head instead of external CSS (fixes WordPress CSS flash).
* Changed custom textarea to sanitize on save and output so line breaks are kept in tact.
* More data sanitization on settings.

= Version 2.0.6 (4/18/13) =

* Fix notice not going away on dismiss.
* Updated translation files.

= Version 2.0.5 (4/17/13) =

* Fix double escaped custom HTML on first import with `wp_specialchars_decode`.
* Add `SHORTINIT` to the PHP stylesheet and script to limit WordPress loading.
* Change wp_cache to transients on PHP scripts and styles, a lot faster!
* Added a delete transient cache button atop the 'Save Changes' button.

= Version 2.0.4 (4/17/13) =

* Updated JSON file to extenal GitHub.

= Version 2.0.3 (4/17/13) =

* Deactivate if minimum WordPress version isn't met.
* Min WordPress version **3.5**
* Version bump to match readme.txt.
* Turkish translations re-added.
* Added `rgba2hex` function to fix updates where rgba was set.
* Make sure function_exists 'wp_enqueue_media'.

= Version 2.0.2 (4/16/13) =

* Updated templates functions into a class.
* Moved some files.
* Fixed known activation hook not fireing on upgrade.
* Removed post_type and install script.
* Hide WP_LOCAL_DEV queries.

= Version 2.0.1 (4/16/13) =

* Wrap any function that can be replicated in if exists to avoid possible issues.
* Prefix any other function with `ap_` as to make sure nothing is replicated in other plugins.
* Fixed broken logo CSS.
* Add hide WordPress logo to settings.

= Version 2.0.0 (4/15/13) =

* Complete rewrite of plugin
* Uses all new Settings API (extendable for custom extensions)
* Loads 120% faster.
* Quick cache of CSS and JS on login page.


== Upgrade Notice ==

= 2.2.0 =
Now you can auto-install Custom Login extensions with an active license key!

= 2.1.9 =
Happy New Year! Finally fixed the logo issue with WordPress 3.8! If you find this plugin useful please consider donating, I've spend countless hours making it better for you. 

= 2.0.6 =
Updated translation files and fixed dismiss notice from not getting removed.