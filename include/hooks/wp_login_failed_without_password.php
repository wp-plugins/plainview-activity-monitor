<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		Login failure. Censors the password.
	@since		2014-05-04 12:38:03
**/
class wp_login_failed_without_password
	extends wp_login_failed
{
	public function get_description()
	{
		return 'Failed to log in due to non-existent user or bad password. Does not display the password.';
	}

	public function get_hook()
	{
		return 'wp_login_failed';
	}

	public function log()
	{
		$this->__password = '';
		parent::log();
	}
}
