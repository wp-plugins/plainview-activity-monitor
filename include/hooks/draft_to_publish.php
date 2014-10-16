<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		A post is published.
	@since		2014-05-04 21:18:41
**/
class draft_to_publish
	extends transition_post_status
{
	public function get_description()
	{
		return 'A post is published.';
	}

	public function get_verb()
	{
		return 'published';
	}
}
