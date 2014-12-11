<?php

namespace plainview\wordpress\activity_monitor\traits;

trait activities
{
	public function _activities_construct()
	{
		$this->add_action( 'plainview_activity_monitor_get_activity_description', 9 );
	}

	/**
		@brief		Displays the HTML in the data.
		@details	This is a simple fallback, in case no other plugins want to return anything more detailed.
		@since		2014-05-03 23:57:43
	**/
	public function plainview_activity_monitor_get_activity_description( $action )
	{
		$action->data = $action->activity->data->html;
	}
}
