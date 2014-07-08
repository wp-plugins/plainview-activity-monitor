<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		Request a new password.
	@since		2014-05-04 12:38:03
**/
class retrieve_password
	extends hook
{
	public function get_description()
	{
		return 'Request a password reset link be sent.';
	}

	public function log()
	{
		$username = $this->parameters->get( 1 );
		$user = get_user_by( 'login', $username );
		$this->log_hook->user_id = $user->ID;
		$this->html_and_execute( 'Requested a password reset.' );
	}
}
