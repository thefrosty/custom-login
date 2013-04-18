=== Custom Login ===
Contributors: austyfrosty
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7431290
Tags: admin, branding, customization, custom login, login, logo, error, login error, custom login pro
Requires at least: 3.5
Tested up to: 3.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use this plugin to customize your login screen, great for client sites!

== Description ==

**Version 2.0** is now 140% faster in the admin and uses a lot less resources! Minimum WordPress version 3.5.

Join in on the [conversation](http://austinpassy.com/wordpress-plugins/custom-login) on my personal blog.

You can find Custom Login [PRO](http://extendd.com/plugin/custom-login-pro/)  on [Extendd.com](http://extendd.com): A plugin marketplace. New features include faster login loading (no database access), Custom Post Types (for multiple designs) and four default CSS designs. **Custom Login Pro is a completly different plugin than Custom Login**.

https://www.youtube.com/watch?v=XOZwaLwpjNo

= Extensions =

Custom Login 2.0 now has many extensions to make your login page better!

**Available Now**

* <a href="http://extendd.com/plugin/custom-login-stealth-login/" title="Custom Login Stealth Login">Stealth Login</a> - obscure your login URL.

**In Development**

* User Hash Key logins (think generated guest logins with-out having to use the login form).
* Email Logins for usernames.
* 2-step Authentication.
* Custom Login page.
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

* More Plugins: [Extendd](http://extendd.com/)
* My Blog: [http:/austinpassy.com/](http://austinpassy.com/)
* My Twitter: @[TheFrosty](https:/twitter.com/TheFrosty)
* WP Twitter: @[Extendd](https:/twitter.com/WPExtendd)
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
They are additional plugins that add functionality to the Custom Login plugin. Depending of what the extension does. You can find all Custom Login extensions on [extendd.com](http://extendd.com).

= My new settings aren't showing up =
This plugin caches all settings in a transient, try clicking the new 'Update stylesheet' button to delete and refresh the cache.

= Is there a PRO version? =
Why yes there is, you can purchase the [PRO](http://extendd.com/plugin/custom-login-pro/) version on [http://extendd.com](http://extendd.com). Note that since Custom Login 2.0, Custom Login PRO is a completely different plugin.

= Why create this plugin? =
I created this plugin to allow for custom login of any WordPress login screen. See working example on: [Extendd.com](http://extendd.com/wp-login.php?action=login).

= Where can I upload and share my cool login screen? =
Check out the newly created [Flickr group](http://flickr.com/groups/custom-login/)! Upload and add it to our pool!

= I think i want to uninstall =
Just de-active.

== Screenshots ==

Screenshots of working example in our [Flickr group](http://flickr.com/groups/custom-login/)

1. Custom Login Settings page (as of v 2.0).

2. Example of a custom login page using the nyan.cat as a animated background! [see user generated designs](http://flickr.com/groups/custom-login/).

== Changelog ==

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

= 2.0.4 =
Important update to fix transient errors, and possible admin crashes.