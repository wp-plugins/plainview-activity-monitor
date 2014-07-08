<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		User deletion.
	@since		2014-05-04 20:43:14
**/
class wpmu_delete_user
	extends delete_user
{
	public function get_description()
	{
		return 'User deletion in a Wordpress Network.';
	}
}
