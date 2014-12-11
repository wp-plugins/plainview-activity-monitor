<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		User login.
	@since		2014-04-27 16:28:01
**/
class wp_login
	extends hook
{
	public function get_description()
	{
		return 'User logged in.';
	}

	public function get_parameter_count()
	{
		return 2;
	}

	public function log()
	{
		// We need special handling for wp_login since the user is not noted in the get_current_user_id function yet.
		$user = $this->parameters->get( 2 );
		$this->log_hook->user_id = $user->ID;

		$this->html()->append( '<em>User agent:</em> %s', $_SERVER[ 'HTTP_USER_AGENT' ] );
		$this->html_and_execute( $this->html() );
	}
}
