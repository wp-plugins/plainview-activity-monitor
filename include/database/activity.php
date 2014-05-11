<?php

namespace plainview\wordpress\activity_monitor\database;

/**
	@brief		An activity row from the database.
	@since		2014-05-11 09:59:05
**/
class activity
{
	use \plainview\sdk\wordpress\traits\db_aware_object;

	public $id;

	public static function db_table()
	{
		return \plainview\wordpress\activity_monitor\Plainview_Activity_Monitor::instance()->get_table_name( 'activities' );
	}

	public static function keys()
	{
		return [
			'id',
			'blog_id',
			'data',
			'dt_created',
			'hook',
			'ip',
			'user_id',
		];
	}

	public static function keys_to_serialize()
	{
		return [
			'data',
		];
	}

	public static function serialize_keys( $o )
	{
		$o->data = $o->data->serialize();
		\plainview\sdk\wordpress\traits\db_aware_object::serialize_keys( $o );
	}
}