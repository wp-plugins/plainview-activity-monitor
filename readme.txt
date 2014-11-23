=== Plainview Activity Monitor ===
Contributors: edward_plainview
License: GPLv3
Requires at least: 3.9
Stable tag: trunk
Tags: activity monitor, activities, monitor, security, ddos, brute force
Tested up to: 4.0

Plugin for PHP v5.4+ that monitors Wordpress actions for user activity on a blog or network.

== Description ==

Plugin for PHP v5.4+ that monitors Wordpress actions for user activity on a blog or network.

Currently monitored actions:

* activated_plugin
* admin_head for views in the admin interface
* deactivated_plugin
* delete_post
* delete_user
* draft_to_publish
* password_reset
* profile_update
* publish_to_publish
* publish_to_trash
* retrieve_password
* trash_to_publish
* user_register
* wp_head for views on the front-end
* wp_login
* wp_login_failed
* wp_login_failed but without logging the attempted password
* wp_logout
* wp_set_comment_status
* wpmu_delete_user

The logged information consists of:

* A description of what was logged
* Blog
* Timestamp
* Hook that was triggered
* User ID

The activities can then be shown in global table showing activities on the whole network, or locally for just the blog you are currently viewing.

The activites can also be filtered so that only specific blogs / hooks / IPs / users are displayed.

= Plugin Pack =

The <a href="http://plugins.plainview.se/plainview-activity-monitor-plugin-pack/" title="Activity Monitor Plugin Packs's page on the web"><em>Activity Monitor Plugin Pack</em></a> is an actively maintained collection of plugins that expand the functionality of Activity Monitor.

Currently the plugin pack offers:

* <a title="Bruteforce Detect AM plugin" href="http://plugins.plainview.se/plainview-activity-monitor-plugin-pack/bruteforce-detect-am-plugin/">Bruteforce Detect</a> monitors failed logins and fires an action when limits have been reached.
* <a title="Content Watch AM plugin" href="http://plugins.plainview.se/plainview-activity-monitor-plugin-pack/content-watch-am-plugin/">Content Watch</a> monitors posts and pages for phrases and fires an action when a phrase is detected.
* <a title="IP Too Often AM plugin" href="http://plugins.plainview.se/ip-too-often-am-plugin/">IP Too Often</a> reacts when an IP causes specific action(s) to occur too often.
* <a title="Login Failed Username AM plugin" href="http://plugins.plainview.se/plainview-activity-monitor-plugin-pack/login-failed-username-am-plugin/">Login Failed Username</a> fires an action when a banned username fails to login.
* <a title="Send To CloudFlare AM plugin" href="http://plugins.plainview.se/plainview-activity-monitor-plugin-pack/send-to-cloudflare-am-plugin/">Send To CloudFlare</a> sends IPs to CloudFlare to be whitelisted / banned when hook(s) are encountered.
* <a title="Send To E-mail AM plugin" href="http://plugins.plainview.se/plainview-activity-monitor-plugin-pack/send-to-e-mail-am-plugin/">Send To E-mail</a> sends an e-mail when hook(s) are encountered.
* <a title="Send To Exec AM plugin" href="http://plugins.plainview.se/plainview-activity-monitor-plugin-pack/send-to-exec-am-plugin/">Send To Exec</a> runs an executable command when hook(s) are encountered.

= Security tips =

There are several ways for people to break in to your Wordpress installation, or cause trouble by DDOS. Here are some tips on how to use the Activity Monitor and its plugins to help detect problems:

* Get a DDOS protection service with an API. There is a plugin to ban IPs via CloudFlare (<em>Send To CloudFlare</em>). Other APIs could be supported as the need arises.
* If you have another DDOS service, write a script that can ban visitors by IP. Use this script with the <em>Send To Exec</em> plugin.
* If you can't ban users using a script, at least set up the <em>Send To E-mail</em> plugin to inform you of suspicious activity.
* Use the Bruteforce Detect plugin to detect when an IP or IPs are trying to guess the admin's password. Ban the IPs automatically using <em>Send To Exec</em>.
* Do not use admin as the username for your administrator account. Instead, use some else and add the admin username to the list of banned usernames in the <em>Login Failed Username</em> plugin. Ban the IPs that cause the plugin to react.

= Custom hooks =

If you are a plugin developer and wish to log your custom hooks, build a plugin that extends an Activity Monitor hook!

Step 1: Create a custom hook. See any of the hooks in the `include/hooks` directory for examples.

Step 2: In your constructor, hook into the `plainview_activity_monitor_loaded` action.

`add_action( 'plainview_activity_monitor_loaded', 'load_my_hooks' );`

Step 3: Load your hooks.

`public function load_my_hooks( $action )
{
	$class = __NAMESPACE__ . '\\hooks\\my_example_hook1';
	$hook = new $class;
	$hook->register_with( $action->hooks );

	$class = __NAMESPACE__ . '\\hooks\\my_example_hook2';
	$hook = new $class;
	$hook->register_with( $action->hooks );
}`

Step 4: Hook into plainview_activity_monitor_manifest_hooks and then register your hook.

This shows your action hooks to all the other Activity Monitor plugins.

`add_action( 'plainview_activity_monitor_manifest_hooks', 'my_example_manifest_hooks' );

function my_example_manifest_hooks( $action )
{
	$hook = new my_example_hook();
	$my_example_hook->register_with( $action->hooks );
}`

Done!

Flesh out the log() or log_post() method in your hook class with something and then wait for the hook to be called.

== Installation ==

1. Check that your web host has PHP v5.4.
1. Activate the plugin locally or sitewide.
1. Go to Activity Monitor > Admin Settings Hooks and deactivate those hooks that are not important.

== Screenshots ==

1. Global activity overview
2. Local activity overview
3. Display and filter settings
4. Tools tab
5. Admin settings tab
6. Hook activation / deactivation
7. Uninstall tab that removes the plugin from the database
8. Plugin Pack overview with extra plugins

== Frequently Asked Questions ==

= I need support! =

Use the Wordpress support forum.

= I do not have PHP 5.4. Can I run the Activity Monitor? =

No. PHP 5.3 is no longer officially supported. It is time for your web host to upgrade.

== Changelog ==

= 20141123 =
* New hooks: activated_plugin and deactivated_plugin.
* Code: Enable same action classes to report different hooks.

= 20141023 =
* New action: wp_head is triggered when a visitor views the front-end. Note that the action is not triggered if you have caching enabled.
* New action: admin_head for views to the admin panel.
* Code: Hide sprintf error in hook data.

= 20141016 =
* Fix: Activity timestamps use local timezone when being displayed.

= 20141013 =
* Fix: If on a network, only super admins may see the menu.

= 20141006 =
* Fix: sprintf error is hidden to prevent problems.
* New: Plugin pack released.

= 20140814 =
* Fix: Prune activities bug fixed.

= 20140708 =
* New: Prune setting keeps a maximum amount of activities in the database. Standard is one million rows.
* Fix: IP filtering in the overview works now.
* Code: Hook->get_vendor, get_description and get_hook are not static anymore.
* Code: New action: get_logged_hooks allows plugins to add hooks dynamically.
* Code: Hooks have their name stored in ->hook.

= 20140623 =
* Fix: Posts without titles get their ID as the title.

= 20140615 =
* New: Better compatability with Windows servers.
* Code: SDK update.

= 20140605 =

* New: Table has CSS classes.
* Fix: Fix for single blog installs.

= 20140521 =

* Fix: Better support for single blog installs (switch_to_blog() checks).

= 20140520 =

* Code: Hooks are loaded after Wordpress has finished loading.

= 20140511 =

* Initial version.
