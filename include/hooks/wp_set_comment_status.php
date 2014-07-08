<?php

namespace plainview\wordpress\activity_monitor\hooks;

/**
	@brief		Comment approved, reset, deleted, etc.
	@since		2014-05-04 21:18:41
**/
class wp_set_comment_status
	extends hook
{
	public function get_description()
	{
		return 'Comment approved, reset, deleted, etc.';
	}

	public function get_parameter_count()
	{
		return 2;
	}

	public function log()
	{
		$comment_id = $this->parameters->get( 1 );
		$comment_data = get_comment( $comment_id );

		// Wordpress is autocleaning things.
		if ( $comment_data === null )
			return;

		$post_id = $comment_data->comment_post_ID;

		switch( $this->parameters->get( 2 ) )
		{
			case '0':
				$verb = 'reset';
				break;
			case '1':
				$verb = 'reapproved';
				break;
			case 'hold':
				$verb = 'held back';
				break;
			case 'spam':
				$verb = 'spammed';
				break;
			case 'trash':
				$verb = 'trashed';
				break;
			case 'delete':
				$verb = 'deleted';
				break;
			default:
				$verb = 'approved';
				break;
		}

		$url = sprintf( '%s#comment-%s', get_permalink( $post_id ), $comment_id );

		$this->html_and_execute( 'Comment <a href="%s">%s</a>.', $url, $verb );
	}
}
