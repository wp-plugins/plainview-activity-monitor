<?php

namespace plainview\wordpress\activity_monitor\actions;

/**
	@brief		Tell all plugins to report in which hooks are available for monitoring.
	@since		2014-04-27 15:37:13
**/
class manifest_hooks
	extends action
{
	/**
		@brief		The hooks container.
		@since		2014-04-27 15:38:07
	**/
	public $hooks;

	/**
		@brief		Constructor.
		@since		2014-04-27 15:37:37
	**/
	public function _construct()
	{
		$this->hooks = new \plainview\wordpress\activity_monitor\hooks\collection;
	}

	/**
		@brief		Sort the hooks by name and vendor.
		@since		2014-05-03 09:22:04
	**/
	public function sort()
	{
		$this->hooks->sort_by( function( $hook )
		{
			return $hook->get_hook() . $hook->get_vendor() . $hook->get_description();
		});
	}
}
