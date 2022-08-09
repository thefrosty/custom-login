=== Custom Login ===
Contributors: austyfrosty, frostymedia
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7431290
Tags: admin, branding, customization, custom login, login, logo, error, login error, custom login pro
Requires at least: 5.8
Tested up to: 6.0.1
Requires PHP: 7.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom Login allows you to easily customize your admin login page, works great for client sites!

== Description ==

Custom Login 3.0 was 200% faster than version 2.0, and version 4.0 is now even better!

For more information visit the official [Custom Login](https://frosty.media/plugins/custom-login/) page.

> <strong>Support</strong><br>
> [Austin](http://austin.passy.co) and the [Frosty Media](https://frosty.media/) team will always try our best to support the Custom Login plugin on the WordPress.org forum, but please note that we can not guarantee a response in a timely manner. If you have an issue we would appriciate you using GitHub or purchasing priority support on our site.
>
> Any extensions purchased on [Frosty Media](https://frosty.media/) (not hosted on WordPress.org) will not be supported on the WordPress.org forum. You can always browse our *small* but growing [documentation](https://frosty.media/docs) for further assistance. You need a valid license key to make support submissions *on our site*. We thank you in advance.

> <strong>Bug Reports</strong><br>
> Bug reports for Custom Login are [welcomed on GitHub](https://github.com/thefrosty/custom-login).

= Video =

http://www.youtube.com/watch?v=hZkc-t36xYQ

= Extensions =

There are currently 7 premium extensions available, with more coming (suggestions welcome - and *will be offered for free to said user*).

**Extensions available now**

* [Stealth Login](https://frosty.media/plugins/custom-login-stealth-login/?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt "Custom Login Stealth Login") - obscure your login URL.
* [Page Template](https://frosty.media/plugins/custom-login-page-template/?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt "Custom Login Page Template") - add a login form to any WordPress page.
* [Login Redirects](https://frosty.media/plugins/custom-login-redirects/?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt "Custom Login Redirects") - Manage login redirects.
* [No Password](https://frosty.media/plugins/custom-login-no-password-login/?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt "Custom Login No Password logins") - allow users to login without a password.
* [Style Pack #1](https://frosty.media/plugins/custom-login-style-pack-1?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt "Custom Login Style Pack #1") - four pre-designed login styles.
* [Style Pack #2](https://frosty.media/plugins/custom-login-style-pack-2?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt "Custom Login Style Pack #2") - four pre-designed fun login styles.
* [Style Pack #3](https://frosty.media/plugins/custom-login-style-pack-3?utm_source=wordpressorg&utm_medium=custom-login&utm_campaign=readme.txt "Custom Login Style Pack #3") - four pre-designed holiday login styles.

**Extensions in development/extension ideas**

* Email Logins for usernames.
* 2-step Authentication.
* "Super User" only access for client sites.
* **Added in core as of version 3.0** Remove default WordPress login CSS.
* Submit button styles!
* **Added as of version 3.2** Custom Login pre-made settings templates *AKA* [Style Packs](https://frosty.media/plugin/tag/style-pack/).

= More info =

Activate the plugin and customize your WordPress login screen. It's as easy as modifying a few settings, there is no need to understand CSS at all. Custom Login even has a HTML, CSS &amp; jQuery textarea for more advanced customizations.

1. Works great for client site installs.
2. Read more about [Custom Login 4.0](https://frosty.media/2022/custom-login-4-0-x-released/)
3. Read more about [Custom Login 3.1](https://frosty.media/2015/custom-login-v3-1-released/)
4. Read more about [Custom Login 2.0](http://wp.me/pzgsJ-HY)

**For those looking to show off your login screen, check out the [Flickr group](http://flickr.com/groups/custom-login/)! Share you designs with the community!**

= links =

* Premium Plugins: [https://frosty.media/plugins](https://frosty.media/plugins/ "Premium WordPress Plugins by Frosty")
* Austins Blog: [https:/austin.passy.co/](http://austin.passy.co/ "Austin's blog")
* Austin on Twitter: @[TheFrosty](https:/twitter.com/TheFrosty "Austin on Twitter")
* Frosty Media on Twitter: @[Frosty_Media](https:/twitter.com/Frosty_Media "Frosty Media on Twitter")
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
This plugin caches all settings in a transient, try clicking the new 'Update stylesheet' button to delete and refresh the cache. *This will apply to versions < 3.0*.

= Why create this plugin? =
I created this plugin to allow for custom login of any WordPress login screen. See working example on: [Frosty.Media/login](http://frosty.media/wp-login.php).

= Where can I upload and share my cool login screen? =
Check out the [Flickr group](http://flickr.com/groups/custom-login/)! Upload and add it to our pool!

= I think I want to uninstall =
Just deactivate it. Sad panda is sad.

== Screenshots ==

Custom Login showcase on the [Flickr group](http://flickr.com/groups/custom-login/).

1. Custom Login v3 Design Settings part 1.

2. Custom Login v3 Design Settings part 2.

3. Custom Login v3 General Settings.

4. Custom Login Extensions Installer (an active license key is required).

== Changelog ==

= Version 4.0.7 (2022/08/09) =

* Don't type cast the return value of removeLostPasswordText.
* Uncomment update option to resolve upgrade notice always showing.
* Don't show the tracking notice, when opt-in or opt-out has already been selected.
* Update WpSettingsApi, and use new condition checks for sidebar(s).
* Update missing autoload notice message.

= Version 4.0.6 (2022/08/08) =

* Fix: Resolve micro regression with `psr/container` issues.
* Fix: Resolve file (URL) fields breaking on save.
* Update WP Utilities to 2.8.
* Update WP Settings API to 3.6.

= Version 4.0.5 (2022/08/06) =

* Make sure autoloading is working before loading classes on plugin uninstall.

= Version 4.0.4 (2022/08/05) =

* Change PSR Container package to use version 1 instead of 2 to avoid WooCommerce errors.

= Version 4.0.3 (2022/08/04) =

* Update both README's with correct "requires at least", "tested up to", and "requires PHP" header tags.
* Cleanup admin notice messages when "unable to activate" due to invalid PHP version or missing autoload.

= Version 4.0.2 (2022/07/15) =

* Don't show error message when not in admin.

= Version 4.0.1 (2022/05/23) =

* Version bump, including manually adding missing vendor directory (need to fix GitHub action).

= Version 4.0.0 (2022/05/23) =

* Complete rewrite.
* Required PHP >= 7.4.
* Full Changelog: [3.2.15...4.0.0](https://github.com/thefrosty/custom-login/compare/3.2.15...4.0.0)

= Version 3.2.15 (2022/03/11) =

* Fix settings API Settings: Uncaught Error: Call to undefined method stdClass::get_permalink() (#49)

= Version 3.2.14 (2022/03/10) =

* Fix settings widget feed for available extensions. Changes from RSS to REST.
* Add auto deploy to WordPress.org GitHub action.

= Version 3.2.13 (2022/01/06) =

* Change custom CSS settings sanitizer in admin to allow for proper HTML CSS attributes like `>`.

= Version 3.2.12 (2021/11/07) =

* Change dashboard from RSS feed to REST API endpoint.
* Fix condition for dashboard widget to use `is_blog_admin`.

= Version 3.2.11 (2020/12/07) =

* PHP 8 compatibility fixes.
* Tested on WordPress 5.6

= Version 3.2.10 (2020/08/14) =

* Tested up to WordPress 5.5
* Fix toggle on/off on settings page, incorrect check in AJAX for new installs.
* Enable on activation hook fixed.
* Remove ace.js theme call to non-existing theme.

== Upgrade Notice ==

= 4.0.1 =
Requires WordPress version >= 5.8 and PHP version >= 7.4.

= 3.2.14 =
Getting ready for version 4.0.0 which will bump minimum required WordPress version to 5.8 and PHP version to 7.4.
