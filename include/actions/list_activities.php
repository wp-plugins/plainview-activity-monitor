<?php

namespace plainview\wordpress\activity_monitor\actions;

use \plainview\sdk\collections\collection;

/**
	@brief		Return an array of activity DB objects.
	@since		2014-05-11 09:57:48
**/
class list_activities
	extends action
{
	/**
		@brief		Fetch results in ascending or descending value?
		@since		2014-05-03 19:26:36
	**/
	public $ascending = false;

	/**
		@brief		Collection of blog IDs to ignore.
		@since		2014-05-03 19:28:24
	**/
	public $blog_ids_to_exclude;

	/**
		@brief		Collection of blog IDs to fetch.
		@since		2014-05-03 19:28:24
	**/
	public $blog_ids_to_include;

	/**
		@brief		Return a count of rows?
		@since		2014-05-03 19:52:45
	**/
	public $count = false;

	/**
		@brief		Date from whence to list. MYSQL DATETIME format.
		@since		2014-07-05 14:39:41
	**/
	public $from_date = null;

	/**
		@brief		Collection of hooks to ignore.
		@since		2014-05-03 19:28:24
	**/
	public $hooks_to_exclude;

	/**
		@brief		Collection of hooks to fetch.
		@since		2014-05-03 19:28:24
	**/
	public $hooks_to_include;

	/**
		@brief		Collection of IPs (as longs) to ignore.
		@since		2014-05-03 19:28:24
	**/
	public $ips_to_exclude;

	/**
		@brief		Collection of IPs (as longs) to fetch.
		@since		2014-05-03 19:28:24
	**/
	public $ips_to_include;

	/**
		@brief		How many rows per page?
		@since		2014-05-03 19:31:30
	**/
	public $per_page = 100;

	/**
		@brief		Page to fetch.
		@since		2014-05-03 19:26:36
	**/
	public $page = 1;

	/**
		@brief		Last date to list. MYSQL DATETIME format.
		@since		2014-07-05 14:39:41
	**/
	public $until_date = null;

	/**
		@brief		Collection of user IDs to ignore.
		@since		2014-05-03 19:28:24
	**/
	public $user_ids_to_exclude;

	/**
		@brief		Collection of user IDs to fetch.
		@since		2014-05-03 19:28:24
	**/
	public $user_ids_to_include;

	/**
		@brief		Constructor.
		@since		2014-05-03 19:27:48
	**/
	public function __construct()
	{
		foreach( static::get_keys() as $key )
			$this->$key = new collection;
	}

	/**
		@brief		Return an array of all of the available filter settings.
		@since		2014-05-04 11:38:09
	**/
	public static function get_keys()
	{
		$r = [];
		foreach( [ 'include', 'exclude' ] as $type )
			foreach( [ 'blog_ids', 'hooks', 'ips', 'user_ids' ] as $key )
			{
				$key = $key . '_to_' . $type;
				$r[] = $key;
			}
		return $r;
	}

}
