=== Plainview Activity Monitor ===
Contributors: edward_plainview
Donate link: http://plainview.se/donate/
License: GPLv3
Requires at least: 3.9
Stable tag: trunk
Tags: activity monitor, activities, security
Tested up to: 3.9.1

Plugin for PHP v5.4+ that monitors Wordpress hooks for user activity on a blog or network.

== Description ==

Plugin for PHP v5.4+ that monitors Wordpress hooks for user activity on a blog or network.

Currently monitored hooks:

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

= Custom hooks =

If you are a plugin developer and wish to log your custom hooks, build a plugin that extends an Activity Monitor hook!

Step 1: Make sure that the Activity Monitor is available:

`if ( ! class_exists( '\\plainview\\wordpress\\activity_monitor\\Plainview_Activity_Monitor' ) )
	return;
`
Step 2: Create a custom hook. See any of the hooks in the `include/hooks` directory for examples.

Step 3: Hook into plainview_activity_monitor_manifest_hooks and then register your hook.

`add_action( 'plainview_activity_monitor_manifest_hooks', 'my_example_manifest_hooks' );

function my_example_manifest_hooks( $action )
{
	$hook = new my_example_hook();
	$my_example_hook->register_with( $action->hooks );
}`

Done!

Flesh out the log() or log_post() method in your hook class with something, enable the hook in the Activity Monitor settings and then wait for the hook to be called.

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

== Frequently Asked Questions ==

= I need support! =

Use the Wordpress support forum.

= I do not have PHP 5.4. Can I run the Activity Monitor? =

No. PHP 5.4 is required and has been released long enough for hosts to have had ample time to update.

== Changelog ==

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
