<?php

namespace plainview\wordpress\activity_monitor\traits;

use plainview\wordpress\activity_monitor\hooks\collection as logged_hooks;

/**
	@brief		Hook-related methods.
	@since		2014-05-03 09:04:32
**/
trait hooks
{
	/**
		@brief		Constructor this these related traits.
		@since		2014-05-03 09:06:33
	**/
	public function _hooks_construct()
	{
		$this->add_action( 'plainview_activity_monitor_get_logged_hooks', 9 );
		$this->add_action( 'plainview_activity_monitor_manifest_hooks', 9 );
	}

	/**
		@brief		Adds all of our watched hooks.
		@since		2014-04-27 20:50:43
	**/
	public function add_hooks()
	{
		$resave = false;
		foreach( $this->get_logged_hooks() as $index => $classname )
		{
			if ( ! class_exists( $classname ) )
			{
				unset( $logged_hooks[ $index ] );
				$resave = true;
				continue;
			}

			$class = new $classname;
			$class->register_with( $this->logged_hooks() );
			$class->hook();
		}

		if ( $resave )
			$this->logged_hooks()->save();
	}

	/**
		@brief		Return an array of logged hooks.
		@since		2014-05-09 12:16:04
	**/
	public function get_logged_hooks()
	{
		$r = $this->get_site_option( 'logged_hooks', [] );
		if ( ! is_array( $r ) )
			$r = [];
		return $r;
	}

	/**
		@brief		Return the collection of logged hooks that are loaded during startup.
		@since		2014-05-06 22:17:04
	**/
	public function logged_hooks()
	{
		if ( !isset( $this->__logged_hooks ) )
		{
			$this->__logged_hooks = new logged_hooks();
			$this->__logged_hooks->activity_monitor = $this;
		}
		return $this->__logged_hooks;
	}

	/**
		@brief		Fill in which hooks we are set to log.
		@since		2014-07-06 17:43:01
	**/
	public function plainview_activity_monitor_get_logged_hooks( $action )
	{
		$action->logged_hooks = $this->logged_hooks();
	}

	/**
		@brief		Add all of our hooks to the collection.
		@since		2014-04-27 15:48:31
	**/
	public function plainview_activity_monitor_manifest_hooks( $action )
	{
		foreach ( [
			'delete_post',
			'delete_user',
			'draft_to_publish',
			'password_reset',
			'profile_update',
			'publish_to_publish',
			'publish_to_trash',
			'retrieve_password',
			'trash_to_publish',
			'user_register',
			'wp_login',
			'wp_login_failed',
			'wp_login_failed_without_password',
			'wp_logout',
			'wp_set_comment_status',
			'wpmu_delete_user',
		] as $hook )
		{
			$class = '\\plainview\\wordpress\\activity_monitor\\hooks\\' . $hook;
			$class = new $class;
			$class->register_with( $action->hooks );
		}
	}

	/**
		@brief		Sets the list of logged hooks.
		@since		2014-05-09 12:14:56
	**/
	public function set_logged_hooks( $logged_hooks )
	{
		$this->update_site_option( 'logged_hooks', $logged_hooks );
	}
}
