<?php

namespace plainview\wordpress\activity_monitor;

use \plainview\sdk_pvam\collections\collection;

class Plainview_Activity_Monitor
	extends \plainview\sdk_pvam\wordpress\base
{
	use traits\activities;
	use traits\database;
	use traits\hooks;
	use traits\menus\base;
	use \plainview\sdk_pvam\wordpress\traits\debug;

	public function _construct()
	{
		$this->_activities_construct();
		$this->_admin_menu_construct();
		$this->_database_construct();
		$this->_hooks_construct();

		$this->add_action( 'plugins_loaded', 'wp_loaded' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Activate / Deactivate
	// --------------------------------------------------------------------------------------------

	public function activate()
	{
		$db_ver = $this->get_site_option( 'database_version', 0 );

		if ( $db_ver < 1 )
		{
			$query = sprintf( 'CREATE TABLE IF NOT EXISTS `%s` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT "Row ID",
			  `blog_id` int(11) NOT NULL COMMENT "ID of blog",
			  `data` longtext NOT NULL COMMENT "Serialized data object",
			  `dt_created` datetime NOT NULL COMMENT "When the activity was created",
			  `hook` varchar(128) NOT NULL COMMENT "Wordpress hook name",
			  `ip` bigint(11) NOT NULL COMMENT "IP address of user",
			  `user_id` int(11) NOT NULL COMMENT "ID of user",
			  PRIMARY KEY (`id`),
			  KEY `indexes` ( `ip`,`blog_id`, `hook`, `user_id` )
			  )', static::get_table_name( 'activities' ) );
			$this->query( $query );

			// Activate all available hooks
			$manifest_hooks = new actions\manifest_hooks;
			$manifest_hooks->execute();
			foreach( $manifest_hooks->hooks as $hook )
				$logged_hooks[ $hook->get_id() ] = $hook->get_id();
			$this->update_site_option( 'logged_hooks', $logged_hooks );

			$db_ver = 1;
		}

		if ( $db_ver < 2 )
		{
			// Update the logged hooks.
			$logged_hooks = $this->get_site_option( 'logged_hooks', [] );
			$r = [];
			foreach( $logged_hooks as $hook )
			{
				if ( ! class_exists( $hook ) )
					continue;
				$class = new $hook;
				$id = $class->get_id();
				$r[ $id ] = $id;
			}
			$this->update_site_option( 'logged_hooks', $r );

			$db_ver = 2;
		}

		$this->update_site_option( 'database_version', $db_ver );
	}

	public function uninstall()
	{
		$query = sprintf( 'DROP TABLE IF EXISTS `%s`', static::get_table_name( 'activities' ) );
		$this->query( $query );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- MISC
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Retrieve the global cache object.
		@since		2014-05-10 23:12:50
	**/
	public function cache()
	{
		if ( isset( $this->__cache ) )
			return $this->__cache;
		$this->__cache = new database\cache;
		return $this->__cache;
	}

	public function site_options()
	{
		return array_merge( [
			'database_version' => 0,							// Version of the database.
			'debug' => false,									// Display debug information?
			'debug_ips' => '',									// List of IP addresses that can see debug information, when debug is enabled.
			'logged_hooks' => [],								// Array of activity class names.
			'activities_in_database' => 1000000,				// How many activities to keep in the database.
			'per_page' => 100,									// How many items to show per table page.
		], parent::site_options() );
	}

	public function wp_loaded()
	{
		// Tell everyone we're finished loading!
		$action = new actions\loaded();
		$action->execute();

		// Manifest and hook them all.
		$action = new \plainview\wordpress\activity_monitor\actions\manifest_hooks;
		$action->execute();
		$action->sort();
		foreach( $action->hooks as $hook )
			$hook->hook();
	}
}
