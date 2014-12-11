<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		Reset the password.
	@since		2014-05-04 12:38:03
**/
class password_reset
	extends hook
{
	public function get_description()
	{
		return 'Reset the password.';
	}

	public function log()
	{
		$this->log_hook->user_id = $this->parameters->get( 1 )->ID;
		$this->html_and_execute( 'Password has been reset.' );
	}
}
