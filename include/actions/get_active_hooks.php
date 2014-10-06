<?php

namespace plainview\wordpress\activity_monitor\actions;

/**
	@brief		Return a collection of all hooks that are active in the system.
	@details	Active hooks are fired but not necessarily logged.
	@since		2014-10-04 08:32:36
**/
class get_active_hooks
	extends action
{
	/**
		@brief		OUT: A hooks\collection of hook objects.
		@since		2014-10-04 08:33:19
	**/
	public $active_hooks;
}
