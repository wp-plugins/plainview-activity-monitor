<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		User creation.
	@since		2014-05-04 20:43:14
**/
class user_register
	extends hook
{
	public function get_description()
	{
		return 'User registration.';
	}

	public function log()
	{
		$user = get_userdata( $this->parameters->get( 1 ) );
		$this->log_hook->user_id = $user->ID;

		$this->html()->append( '<em>Login name:</em> %s', $user->data->user_login );
		$this->html()->append( '<em>Email:</em> %s', $user->data->user_email );
		$this->html_and_execute( $this->html() );
	}
}
