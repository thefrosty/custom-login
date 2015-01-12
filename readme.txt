=== Custom Login ===
Contributors: austyfrosty, frostymedia
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7431290
Tags: admin, branding, customization, custom login, login, logo, error, login error, custom login pro
Requires at least: 4.0
Tested up to: 4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custin Login allows you to easily customize your admin login page, works great for client sites!

== Description ==

Custom Login 2.0 was 140% faster than version 1.0, and version 3.0 is now even better! *Minimum WordPress version is 4.0*

For more information visit the official [Custom Login](http://frosty.media/plugins/custom-login/) page.

> <strong>Support</strong><br>
> [Austin](http://austin.passy.co) and the [Frosty Media](http://frosty.media/) team will always try our best to support the Custom Login plugin on the WordPress.org forum, but please note that we can not guarantee a response in a timely manner. If you have an issue we would appriciate you using GitHub or purchasing priority support on our site.
>
> Any extensions purchased on [Frosty Media](http://frosty.media/) (not hosted on WordPress.org) will not be supported on the WordPress.org forum. You can always browse our *small* but growing [documentation](http://frosty.media/docs) for further assistance. You need a valid license key to make support submissions *on our site*. We thank you in advance. 

> <strong>Bug Reports</strong><br>
> Bug reports for Custom Login are [welcomed on GitHub](https://github.com/thefrosty/custom-login). 

= Video =

http://www.youtube.com/watch?v=hZkc-t36xYQ

= Extensions =

There are currently 4 premium extensions available, with more coming (suggestions welcome - and *will be offered for free to said user*).

**Extensions available now**

* <a href="http://frosty.media/plugins/custom-login-stealth-login/?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt" title="Custom Login Stealth Login">Stealth Login</a> - obscure your login URL.
* <a href="http://frosty.media/plugins/custom-login-page-template/?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt" title="Custom Login Page Template">Page Template</a> - add a login form to any WordPress page.
* <a href="http://frosty.media/plugins/custom-login-redirects/?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt" title="Custom Login Redirects">Login Redirects</a> - Manage login redirects.
* <a href="http://frosty.media/plugins/custom-login-no-password-login/?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt" title="Custom Login No Password logins">No Password</a> - allow users to login without a password. 

**Extensions in development/extension ideas**

* Email Logins for usernames.
* 2-step Authentication.
* "Super User" only access for client sites.
* **Added in core as of version 3.0** Remove default WordPress login CSS.
* Submit button styles!
* Custom Login pre made settings templates.

= More info =

Activate the plugin and customize your WordPress login screen. It's as easy as modifying a few settings, there is no need to understand CSS at all. Custom Login even has a HTML, CSS &amp; jQuery textarea for more advanced customizations.

1. Works great for client site installs.
2. Read more about [Custom Login](http://wp.me/pzgsJ-HY) 2.0

**For those looking to showoff your login screen, check out the [Flickr group](http://flickr.com/groups/custom-login/)! Share you designs with the community!**

= links =

* Premium Plugins: [https://frosty.media/plugins](https://frosty.media/plugins/ "Premium WordPress Plugins by Frosty")
* Austins Blog: [https:/austin.passy.co/](https://austin.passy.co/ "Austin Passy's blog")
* Austin on Twitter: @[TheFrosty](https:/twitter.com/TheFrosty "Austin TheFrosty' Passy on Twitter")
* Frosty Media on Twitter: @[FrostyMediaWP](https:/twitter.com/FrostyMediaWP "Extendd on Twitter")
* **Development welcomed on [GitHub](https://github.com/thefrosty/custom-login)**

= Hooks and Filters =

Coming Soon.

== Installation ==

Follow the steps below to install the plugin.

1. Upload the `custom-login` directory to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'plugins' menu in WordPress.
3. Go to Settings/custom-login to edit your settings.
4. Design away.
5. Activate the settings by checking "Enable" in the "General Settings" tab.

== Frequently Asked Questions ==

= What are extensions? =
They are additional plugins that add/extend functionality to the Custom Login plugin. You can find all Custom Login extensions on [Frosty.Media](http://frosty.media).

= My new settings aren't showing up =
This plugin caches all settings in a transient, try clicking the new 'Update stylesheet' button to delete and refresh the cache. *This applys to versions < 3.0*.

= Is there a PRO version? =
Well, yes and no. Since version 3.0 of Custom Login the [PRO](http://frosty.media/plugins/custom-login-pro/) version is a completely different plugin. Instead of settings to manage your login design there is a new post type where you can create unlimited "designs" and activate each one as needed. Since version 3.0 all Custom Login extensions no longer work with Custom Login Pro, but will be merged into it in the future.

= Why create this plugin? =
I created this plugin to allow for custom login of any WordPress login screen. See working example on: [Frosty.Media/login](http://frosty.media/wp-login.php).

= Where can I upload and share my cool login screen? =
Check out the [Flickr group](http://flickr.com/groups/custom-login/)! Upload and add it to our pool!

= I think i want to uninstall =
Just deactive it. Sad panda is sad.

== Screenshots ==

Custom Login showcase on the [Flickr group](http://flickr.com/groups/custom-login/).

1. Custom Login v3 Design Settings part 1.

2. Custom Login v3 Design Settings part 2.

3. Custom Login v3 General Settings.

4. Custom Login Extensions Installer (an active license key is required).

== Changelog ==

= Version 3.0.3 (12/12/14) =

* Fix: Add missing "Remove WP Logo" setting.
* Fix: When "activate" isn't checked disable settings output.
* Fix: foreach error. When `get_editable_roles` fails to return an array. [forum](https://wordpress.org/support/topic/invalid-argument-supplied-for-foreach-error-line-in-wp-dashboard?replies=2#post-6427631)
* Fix: On Logo "insert" getting called on background image insert.
* Tweak: Update chosen JS to version 1.3.0.

= Version 3.0.2 (12/12/14) =

* Fix: Logo background size width &amp; height settings not transfering over in upgrade process.
* Fix: Checking "Remove lost password text" removes the text instead of the other way around. [forum](https://wordpress.org/support/topic/lost-your-password-1) 

= Version 3.0.1 (12/11/14) =

* New: Add force width option to force width on h1 logo wrapper.
* Bug: Change sanitization of all integer fields to 'int' vs 'absint' to allow empty or no value.
* Bug: Remove is_int function on Logo width and height style output.
* Tweak: On Logo upload and "insert" update the width and height input settings fields for logo with the image size.
* Tweak: Change CSS rule(s) from `#login h1 a` to `.login h1 a`.

= Version 3.0.0 (12/01/14) =

_REQUIRES WordPress 3.9 or later_

* New: Complete rewrite.
* New: Settings page UI update, now matches WordPress color.
* New: Extensions installer moved to sparate settings page (hidden).
* New: Removed version [2.x changelog](http://plugins.svn.wordpress.org/custom-login/tags/2.4/readme.txt).

== Upgrade Notice ==

= 3.0.3 =
Complete rewrite of Custom Login, be sure to run the update script to keep your old settings.