<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		Deactivated a plugin.
	@since		2014-11-23 10:28:44
**/
class deactivated_plugin
	extends hook
{
	use categories\Plugins;

	public function get_description()
	{
		return 'Deactivated a plugin.';
	}

	public function log()
	{
		$this->html_and_execute( 'Deactivated plugin %s', $this->parameters->get( 1 ) );
	}
}
