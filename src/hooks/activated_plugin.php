<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		Activated a plugin.
	@since		2014-11-23 10:28:44
**/
class activated_plugin
	extends hook
{
	use categories\Plugins;

	public function get_description()
	{
		return 'Activated a plugin.';
	}

	public function log()
	{
		$this->html_and_execute( 'Activated plugin %s', $this->parameters->get( 1 ) );
	}
}
