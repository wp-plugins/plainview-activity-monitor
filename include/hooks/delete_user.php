<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		User deletion.
	@since		2014-05-04 20:43:14
**/
class delete_user
	extends user_register
{
	public function get_description()
	{
		return 'User deletion.';
	}
}
