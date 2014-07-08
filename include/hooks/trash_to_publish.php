<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		A post is restored.
	@since		2014-05-04 21:18:41
**/
class trash_to_publish
	extends transition_post_status
{
	public function get_description()
	{
		return 'A post is restored.';
	}

	public function get_verb()
	{
		return 'restored';
	}
}
