<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		Admin page was viewed.
	@since		2014-10-17 07:10:47
**/
class admin_head
	extends hook
{
	use categories\Page_Views;

	public function get_description()
	{
		return 'Admin page was viewed.';
	}

	public function log()
	{
		$link = $this->activity_monitor()->current_url();

		global $admin_title;
		$title = $admin_title;

		$this->html_and_execute( '<a href="%s">%s</a>', $link, $title );
	}
}
