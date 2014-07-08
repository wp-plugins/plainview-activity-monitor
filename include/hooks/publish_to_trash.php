<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		A post is trashed.
	@since		2014-05-04 21:18:41
**/
class publish_to_trash
	extends transition_post_status
{
	public function get_description()
	{
		return 'A post is trashed.';
	}

	public function get_verb()
	{
		return 'trashed';
	}
}
