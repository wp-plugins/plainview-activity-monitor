<?php

namespace plainview\wordpress\activity_monitor\database;

use \plainview\sdk\collections\collection;

/**
	@brief		A user's filters and settings.
	@since		2014-05-11 09:59:37
**/
class filters_settings
	extends \plainview\sdk\collections\collection
{
	/**
		@brief		Constructor.
		@since		2014-05-04 12:04:34
	**/
	public function __construct()
	{
		$this->set( 'filters', new collection() );
		$this->set( 'table_columns', new collection() );
	}

	/**
		@brief		Deletes the filter settings meta for the user.
		@since		2014-05-04 11:33:55
	**/
	public static function delete( $user_id = null )
	{
		if ( ! $user_id )
			$user_id = get_current_user_id();
		delete_user_meta( $user_id, static::get_meta_key() );
	}

	/**
		@brief		Should this table column be displayed?
		@since		2014-05-07 11:54:20
	**/
	public function display_table_column( $column )
	{
		return $this->get( 'table_columns' )->has( $column );
	}

	/**
		@brief		Count how many filters are active.
		@since		2014-05-04 11:47:13
	**/
	public function get_active_filter_count()
	{
		$r = 0;
		foreach( $this->get( 'filters' )->to_array() as $item )
			$r += count( $item );
		return $r;
	}

	/**
		@brief		Get a value, with the default of [].
		@since		2014-05-04 11:22:36
	**/
	public function get_filter( $key, $default = NULL )
	{
		return $this->get( 'filters' )->get( $key, [] );
	}

	/**
		@brief		Gets all of the displayable table columns.
		@since		2014-05-07 11:46:02
	**/
	public function get_table_columns()
	{
		return $this->get( 'table_columns' )->to_array();
	}

	/**
		@brief		Return an array of table columns, for a form2 select.
		@since		2014-05-07 11:50:52
	**/
	public static function get_table_column_options()
	{
		return [
			'Blog' => 'blog_id',
			'Description' => 'description',
			'Hook' => 'hook',
			'IP address' => 'ip',
			'Timestamp' => 'dt_created',
			'User' => 'user_id',
		];
	}

	/**
		@brief		Returns the meta key in which the filter settings are saved.
		@since		2014-05-04 11:20:16
	**/
	public static function get_meta_key()
	{
		return 'pv_am_filters_settings';
	}

	/**
		@brief		Loads the user settings.
		@since		2014-05-04 11:13:10
	**/
	public static function load()
	{
		$r = new filters_settings;
		$user_id = get_current_user_id();
		$meta = get_user_meta( $user_id, static::get_meta_key() );
		$meta = reset( $meta );
		if ( $meta )
			foreach( $meta as $key => $array )
				foreach( $array as $array_key => $array_value )
					$r->get( $key )->set( $array_key, $array_value );
		else
		{
			// No meta? Assume that all table columns are to be displayed.
			foreach( self::get_table_column_options() as $option )
				$r->get( 'table_columns' )->set( $option, $option );
		}
		return $r;
	}

	/**
		@brief		Saves the settings.
		@since		2014-05-04 11:09:35
	**/
	public function save()
	{
		$meta = [
			'filters' => $this->get( 'filters' )->to_array(),
			'table_columns' => $this->get( 'table_columns' )->to_array(),
		];
		$user_id = get_current_user_id();
		update_user_meta( $user_id, static::get_meta_key(), $meta );
	}

	/**
		@brief		Sets a filter.
		@since		2014-05-04 12:10:31
	**/
	public function set_filter( $key, $value )
	{
		return $this->get( 'filters' )->set( $key, $value );
	}

	/**
		@brief		Sets the visible columns of the activities table.
		@since		2014-05-07 11:52:41
	**/
	public function set_table_columns( $columns )
	{
		$this->get( 'table_columns' )->flush();
		foreach( $columns as $column )
			$this->get( 'table_columns' )->set( $column, $column );
	}
}
