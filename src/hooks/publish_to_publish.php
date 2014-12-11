<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		A post is updated.
	@since		2014-05-04 21:18:41
**/
class publish_to_publish
	extends transition_post_status
{
	public function get_description()
	{
		return 'A post is updated.';
	}

	public function get_verb()
	{
		return 'updated';
	}
}
