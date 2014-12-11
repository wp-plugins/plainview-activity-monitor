<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		Login failure.
	@since		2014-05-04 12:38:03
**/
class wp_login_failed
	extends hook
{
	public function get_description()
	{
		return 'Failed to log in due to non-existent user or bad password.';
	}

	public function log()
	{
		if ( ! isset( $this->__password ) )
			$this->__password = esc_html( $_POST['pwd'] );
		if ( ! isset( $this->__username ) )
			$this->__username = esc_html( $_POST['log'] );

		$username = $this->__username;
		$user = get_user_by( 'login', $username );

		if ( ! $user )
			$this->html()->append( 'Invalid username: %s', '<em>' . $username . '</em>' );
		else
		{
			$this->log_hook->user_id = $user->ID;
		}

		if ( $this->__password != '' )
			$this->html()->append( 'Password: %s', '<em>' . $this->__password . '</em>' );

		$this->html()->append( 'User agent: %s', '<em>' . $_SERVER[ 'HTTP_USER_AGENT' ] . '</em>' );

		$this->html_and_execute( $this->html() );
	}
}
