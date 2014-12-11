<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		Profile update.
	@since		2014-05-04 21:18:41
**/
class profile_update
	extends hook
{
	public function get_description()
	{
		return 'Profile update.';
	}

	public function get_parameter_count()
	{
		return 2;
	}

	public function log()
	{
		$new_userdata = get_userdata( $this->parameters->get( 1 ) )->data;
		$old_userdata = $this->parameters->get( 2 );

		if ( $new_userdata->display_name != $old_userdata->display_name )
			$this->html()->append( '<em>Display name</em> changed from <em>%s</em> to <em>%s</em>.', $old_userdata->display_name, $new_userdata->display_name );

		if ( $new_userdata->user_email != $old_userdata->user_email )
			$this->html()->append( '<em>E-mail</em> changed from <em>%s</em> to <em>%s</em>.', $old_userdata->user_email, $new_userdata->user_email );

		if ( $new_userdata->user_pass != $old_userdata->user_pass )
			$this->html()->append( '<em>Password</em> changed.' );

		if ( $this->html()->count() < 1 )
			return;

		$this->html_and_execute( $this->html() );
	}
}
