<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		User logout.
	@since		2014-05-01 22:01:00
**/
class wp_logout
	extends hook
{
	public function get_description()
	{
		return 'User logged out.';
	}

	public function log()
	{
		$this->html_and_execute( 'User %s has logged out.', $this->log_hook->user_id_html() );
	}

}
