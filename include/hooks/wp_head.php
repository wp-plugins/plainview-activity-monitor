<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		A page was visited in the front-end.
	@since		2014-10-17 07:19:36
**/
class wp_head
	extends hook
{
	public function get_description()
	{
		return 'A page was visited in the front-end. Does not work if the pages are cached.';
	}

	public function log()
	{
		$link = $this->activity_monitor()->current_url();
		$title = $link;

		if ( is_single() || is_page() )
			$title = get_the_title();

		$this->html_and_execute( '<a href="%s">%s</a>', $link, $title );
	}
}
