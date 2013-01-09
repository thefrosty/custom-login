=== Custom Login ===
Contributors: austyfrosty
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7431290
Tags: admin, branding, customization, custom login, login, logo, error, login error, custom login pro
Requires at least: 3.4
Tested up to: 3.5
Stable tag: trunk

Use this plugin to customize your login screen, great for client sites!

== Description ==

Join in on the [conversation](http://austinpassy.com/wordpress-plugins/custom-login) on my personal blog. I've also just released a [PRO](http://extendd.com/plugin/custom-login-pro/) version on [Extendd.com](http://extendd.com): A plugin marketplace. New features include faster login loading (no database access), Custom Post Types (for multiple designs) and four default CSS designs.

Activate this plugin and customize your WordPress login screen. Use the built-in and easy to use settings page to do the work for you. Theres no need to understand CSS at all!
Now featureing a HTML &amp; CSS box for advanced users to up the customization!

1. Works great for client site installs.
2. Comes with a Photoshop template included in the library files (default theme).
3. Read more about the [Custom Login Plugin](http://austinpassy.com/wordpress-plugins/custom-login/) & [most recent blog post](http://austinpassy.com/2010/09/custom-login-version-0-8/).

**For those looking to showoff your login screen, check out the [Flickr group](http://flickr.com/groups/custom-login/)! Share you designs with the community!**

= links =

* My portfolio: [http:/frostywebdesigns.com/](http://frostywebdesigns.com/)
* My Blog: [http:/austinpassy.com/](http://austinpassy.com/)
* Twitter: @[TheFrosty](https:/twitter.com/TheFrosty)
* Twitter: @[Extendd](https:/twitter.com/WPExtendd)
* **Contribute on [GitHub](https://github.com/thefrosty/custom-login)**

== Installation ==

Follow the steps below to install the plugin.

1. Upload the `custom-login` directory to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings/custom-login to edit your settings.
4. **Be sure to *check* activate** on the settings page to remove the *default* login design.
5. Design away.


== Frequently Asked Questions ==

= Is there a PRO version? =
Why yes there is, you can purchase the [PRO](http://extendd.com/plugin/custom-login-pro/) version on [http://extendd.com](http://extendd.com).

= Why create this plugin? =
I created this plugin to allow for custom login of any WordPress login screen. See working example on: [Extendd.com](http://extendd.com/wp-login.php?action=login).

= Where can I upload and share my cool login screen? =
Check out the newly created [Flickr group](http://flickr.com/groups/custom-login/)! Upload and add it to our pool!

= I don't like the default login =
Well you should check the first box "`Use your own CSS`" and customize **your** login screen!

= I think i want to uninstall =
Just de-active.

== Screenshots ==

Screenshots of working example in our [Flickr group](http://flickr.com/groups/custom-login/)

1. Custom Login Settings page (as of v 0.8).

2. Example of a custom login page, [see more](http://flickr.com/groups/custom-login/).

== Changelog ==

= Version 1.1.1 (1/9/13) =

* Fixed: html background image css missing definition. [issue](http://wordpress.org/support/topic/error-with-version-110?replies=2)
* Fixed: CSS images need not be escaped. Removed `esc_attr` from `trailingsemicolonit()`.
* Fixed: CSS form background image.

= Version 1.1.0 (1/8/13) =

* Minimun required WordPress version is now *3.4*!
* Updated: `jscolor.js` to version `1.4.0`.
* Updated: Added new textarea autosize script. See [https://github.com/jackmoore/autosize](https://github.com/jackmoore/autosize)
* Changed: HTML background repeat is now a dropdown
* Added: Background size option. [request](http://wordpress.org/support/topic/plugin-custom-login-resize-background-image?replies=2), [request](http://wordpress.org/support/topic/plugin-custom-login-love-the-plugin-and-a-small-wish-list?replies=1)
* Removed registered CSS and added inline style to remove WordPress double loading. Should increase speed by 2X.
* Change: Modify user role from `edit_plugins` to `manage_options` to allow `define('DISALLOW_FILE_EDIT', true);` as per [issue](http://wordpress.org/support/topic/plugin-custom-login-disallow_file_edit-breaks-plugin?replies=3#post-3702957)
* Removed: CHMOD issue. See [issue](http://wordpress.org/support/topic/errors-15?replies=2#post-3702984). Replaces with `site_transient`.
* Removed upgrade to [Pro](http://extendd.com/plugin/custom-login-pro/) script and pages.
* Fixed: possbile PHP > 5.4 object warning [issue](http://wordpress.org/support/topic/custom-login-and-php-54?replies=3), [issue](http://wordpress.org/support/topic/plugin-custom-login-problem-after-installation?replies=2#post-3702975). Let me know if you still get errors.
 

= Version 1.0.4.1 (12/3/12) =

* Updated links.
* Updated dashboard.


= Version 1.0.4 (09/10/12) =

* Added presstrends.io

= Version 1.0.3 (08/16/12) =

* Updated the default CSS.
* Updated admin feeds.

= Version 1.0.2 (04/01/12) =

* Updated `pot` file.
* Updated Turkish launguage file.
* If cache folder isn't writable chmod `666`.

= Version 1.0.1 (04/01/12) =

* Fixed error saving settings in some cases.
* Moved upgrade script to a new page.

= Version 1.0.0 (03/29/12) =

* New default login page (if you've not *activated* the custom `CSS`).
* Added update box to settings page. (If you've already purchased the [PRO](http://thefrosty.com/custom-login-pro) upgrade, login and retreive the download link)
* Cleaned up the dashboard.
* Escaping thefrosty_network_feed().
* Updated Sprite.
* Added Spanish translation. (props Alejandro Arcos)

= Version 0.9.8.2 (03/04/12) =

* Added Turkish translation.

= Version 0.9.8.1 (02/17/12) =

* readme.txt links updated.

= Version 0.9.8 (02/02/12) =

* readme.txt update.
* Description text update.
* Admin bug fix.

= Version 0.9.7 (12/18/11) =

* Updated save settings HTML.

= Version 0.9.6.1 (12/14/11) =

* forgot "()", sorry.

= Version 0.9.6 (12/14/11) =

* Updated minor CSS changes to WordPress 3.3.
* Updated some spelling mistakes.

= Version 0.9.5 (11/8/11) =

* Feeds updated.
* WordPress 3.3 check.

= Version 0.9.3 (9/8/11) =

* Small bug in dashboard.
* jQuery not loading in login head on some rare occasions.

= Version 0.9.3 (7/30/11) =

* Updated `wp_enqueue_style` to `wp_register_style` for the custom CSS that causes issues in WordPress 3.3

= Version 0.9.2 (7/6/11) =

* Added `!important` to the `html` CSS attribute for background because of new CSS rule in WordPress 3.2.

= Version 0.9.1 (6/23/11) =

* Fixed admin jQuery and upload buttons (backwards compatible with jQuery < 1.6)

= Version 0.9.0 (6/23/11) =

* [BUG FIX] An error in the dashboard widget is casuing some large images. Sorry. Always escape.

= Version 0.8.9.2 (5/18/11) =

* Bug Fix: Missing $domain index.

= Version 0.8.9.1 (5/9/11) =

* Not disabling the gravatar feature if registration was disabled. Missing `!`.

= Version 0.8.9 (5/8/11) =

* Added checkbox to turn off dashboard.
* Added `disabled` to gravatar to remove idex error.
* Trying to localize as much of the admin text as possible for translation.

= Version 0.8.8 (3/30/11) =

* Updated out of order tabs.
* Fixed missing index variables that may cause error when runner in debug mode.
* Fixed header output error.
* Updated dashboard widget.

= Version 0.8.7 (2/24/11) =

* Removed javascript that was causing hangups.

= Version 0.8.6 (2/9/11) =

* Updated the feed parser to comply with deprecated `rss.php` and use `class-simplepie.php`.

= Version 0.8.5 =

* array_slice error fixed.

= Version 0.8.4.1 =

* Moved priority on Setting page.
* Updated `POT` file.

= Version 0.8.4 =

* Don't add `gravatar.js` if it's not needed.
* fixed bug where multiple installs in a single server of different WP installs exist.
* Set role for settings page from `6` to `edit_plugins` to comply with new Roles and Capabilities.
* 

= Version 0.8.3 =

* Added missing `gravatar.js` file.

= Version 0.8.2 =

* Fixed CSS code on the login page.

= Version 0.8.1 =

* Fixed CSS code that was converting single and double quotes to HTML entities.

= Version 0.8 =

* Important! Users will have to re-save their settings after updating to version 0.8.
* Completely recoded the plugin from the ground up for a much needed code overhaul.
* Removed unistall script.
* Removed [wpads.net](http://wpads.net).
* Cleaned up options.
* Removed easing.js, farbtastic.js, dock.js
* Removed unused images.
* Added `license.txt`, `readme.html`.

= Version 0.7.2 =

* Fixed bug where plugin isn't allowed to be *deleted*(removed) when using WordPress 3.0+.

= Version 0.7.1 =

* Dashboard widget bug, upgrade mandatory.

= Version 0.7 =

* Updated `array_slice` error
* Added `wp_wb_version` check for WordPress 3+
* Added new option for WordPress 3+, *body background*.
* Added check for version in animated error.
** WP3 now includes the jQuery error
* Now 100% compatible with WordPress 3.x

= Version 0.6.1 =

* Turned off the animated Autoresizer for expanding textareas, as it was buggy.

= Version 0.6 =

* Addded custom javascript error animation *turned on by default*
* Cleaned up settings page

= Version 0.5.2 =

* Added dashboard widget.
* Moved preview link higher.
* Bug fix.

= Version 0.5.1 =

* Changes a function name.
* Max character fix in `border-top-color`.

= Version 0.5 =

* **NEW** Javascript color picker [jscolor](http://jscolor.com)
* **NEW** New field allows for colorization of the *body* `border-top` color
* More options added
* Cleaned up code
* Testing new inline ad feature from [wpAds](http://bit.ly/wpadsnet) *Please leave [feedback/comments](http://austinpassy.com/2010/02/custom-login-version-0-5/) on this feature*

= Version 0.4.8 =

* Thickbox preview link :) *Let me know if you like the placement of it [in these comments](http://austinpassy.com/2010/02/custom-login-version-0-4-8/)*

= Version 0.4.7 =

* Added ability to use RGB/A colors to all color selectors. Max characters is now `21` instead of `7`
* Cleaned up options page.
* Added expanding textarea's for better coding space.
* Allow for expanding help section per item basses.
* Added an uninstaller (remove options from the database) *use the uninstall.php script*
* **NEW** default style.

= Version 0.4.6.1 =

* CSS Bug.

= Version 0.4.6 =

* Added custom html coded box. Will only be in use if the box is checked.
* New html box used jQuery to write to the page.

= Version 0.4.5 =

* Removed: #dock scroller (position fixed)
* Added collapsing/expanding boxes on left to allow for visibility of color wheel.

= Version 0.4.4.1 =

* Error: Missed a period, caused fatal error.
* Noticed issue with color picker error, try to reload page while I troubleshoot.
* Fixed missing `div` tag on settings page.
* Added two screenshots, one of settings page, one example.

= Version 0.4.4 =

* Added custom field box to add in your own CSS
* Added in new toggle (hide the color box when you click on the `h3` title so as not to interfere when it auto scrolls)  

= Version 0.4.3 =

* Bug: When first installed, color fields need the `#` before the HEX numbers shows.

= Version 0.4.2 =

* Added an additional save button under the scrolling window dock when the window height is causing the window to jump.

= Version 0.4.1.1 =

* Bug fix: `Dock` is in the wrong div

= Version 0.4.1 =

* Added a `position:fixed` style to the color picker if the window scrolls below the view of the *Color picker*

= Version 0.4 =

* Added jQuery javascript color picker
* Remeber to use the new color selections with **#** before the six `hex` keys!

= Version 0.3.3 =

* Added ability to have transparent background image for `html`
* Added `html > background-repeat`

= Version 0.3.2.1 =

* Style: "Addded css style to `Delete/Reset` button"

= Version 0.3.2 =

* Bug: "if login form background color was empty, image wouldn't show"

= Version 0.3.1 =

* Bug: "login form backgound url" overwrote "login form backgound color"
* Auto install into double directory

= Version 0.3 =

* Admin panel added
* Additional CSS / user options
* Custom login
* WordPress 2.9 ready

= Version 0.2 =

* 2.7 CSS update.
* readme.txt link updated.

= Version 0.1 =

* Initial release.


== Upgrade Notice ==

= 1.1.1 =
Fixes background image issues.

= 1.1.0 =
Fixes a lot of issues reported on the WP forums. Fixes issues with PHP 5.4 object error.

= 1.0.1 =
Fixed save settings and moved upgrade script to new page.

= 1.0 =
Added download script for Custom Login Pro users.

= 0.9.6 =
WordPress 3.3 style registration compatible. Some changes may effect your design, please update accordingly.

= 0.9.3 =
WordPress 3.3 style registration compatible.

= 0.8.10 =
Important! Unescaping characters in the dashboard widget/

= 0.8.8 =
Miscellaneous errors fixed, bugs squashed.

= 0.8 =
Complete rewrite, you will have to re-save your settings!

= 0.7.2 =
Uninstall script no longer valid for WordPress 3.0+

= 0.7.1 =
Compatiblity issue with my other plugin [@Anywhere](http://austinpassy.com/anywhere-plugin) fixed.